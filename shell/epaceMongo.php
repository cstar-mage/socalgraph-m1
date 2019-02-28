<?php

require_once 'abstract.php';

class MongoEpaceCollection
{
    /**
     * @var Blackbox_Epace_Model_Epace_AbstractObject
     */
    protected $epaceResource;
    /**
     * @var MongoDB\Driver\Manager
     */
    protected $manager;
    protected $database;

    /**
     * @var MongoDB\Driver\BulkWrite
     */
    protected $bulkWrite;
    protected $currentBulkWriteIds = [];

    public static $bulkWriteLimit = 1;

    protected $existingIds = [];

    public function __construct($epaceResource, $manager, $database)
    {
        $this->epaceResource = $epaceResource;
        $this->manager = $manager;
        $this->database = $database;
    }

    public function getCollectionName()
    {
        return $this->epaceResource->getObjectType();
    }

    public function getPrimariKey()
    {
        return $this->epaceResource->getIdFieldName();
    }

    public function insertData($data)
    {
        //$this->manager->executeQuery($this->database . '.' . $this->getCollectionName(),)
    }

    public function updateData($data)
    {
        $id = $data[$this->getPrimariKey()];

    }

    public function exists(Blackbox_Epace_Model_Epace_AbstractObject $object)
    {
        $this->validateObject($object);

        return $this->isIdExists($object->getId());
    }

    protected function isIdExists($id)
    {
        if (in_array($id, $this->existingIds)) {
            return true;
        }

        //$id = new \MongoDB\BSON\ObjectID($id);
        $filter = ['_id' => $id];
        $options = [
            'projection' => ['_id' => 0]
        ];

        $query = new \MongoDB\Driver\Query($filter, $options);
        $rows = $this->manager->executeQuery($this->database . '.' . $this->getCollectionName(), $query);

        $result = count($rows->toArray()) > 0;
        if ($result) {
            $this->existingIds[] = $id;
        }

        return $result;
    }

    public function insertIfNotExists(Blackbox_Epace_Model_Epace_AbstractObject $object)
    {
        $this->validateObject($object);

        if (in_array($object->getId(), $this->currentBulkWriteIds) || $this->isIdExists($object->getId())) {
            return;
        }

        if (!$this->bulkWrite) {
            $this->bulkWrite = new MongoDB\Driver\BulkWrite(['ordered' => true]);
        }

        $data = $object->getData();
        $data['_id'] = $object->getId();

        $this->bulkWrite->insert($data);
        $this->currentBulkWriteIds[] = $object->getId();

        if ($this->bulkWrite->count() == self::$bulkWriteLimit) {
            $this->flush();
        }
    }

    public function insertOrUpdate(Blackbox_Epace_Model_Epace_AbstractObject $object)
    {
        $this->validateObject($object);
        if (in_array($object->getId(), $this->currentBulkWriteIds)) {
            return;
        }

        if (!$this->bulkWrite) {
            $this->bulkWrite = new MongoDB\Driver\BulkWrite(['ordered' => true]);
        }

        $this->bulkWrite->update(
            ['_id' => $object->getId()],
            ['$set' => $object->getData()],
            ['multi' => false, 'upsert' => true]
        );
        $this->currentBulkWriteIds[] = $object->getId();

        if ($this->bulkWrite->count() == self::$bulkWriteLimit) {
            $this->flush();
        }
    }

    public function checkCollection()
    {
        $response = $this->manager->executeCommand($this->database, new \MongoDB\Driver\Command(['listCollections' => 1]))->toArray();
        foreach ($response as $collection) {
            if ($collection->name == $this->getCollectionName()) {
                return;
            }
        }
        $response = $this->manager->executeCommand($this->database, new \MongoDB\Driver\Command(['create' => $this->getCollectionName()]));
    }

    public function flush()
    {
        if ($this->bulkWrite && $this->bulkWrite->count() > 0) {
            $this->manager->executeBulkWrite($this->database . '.' . $this->getCollectionName(), $this->bulkWrite);
            $this->bulkWrite = null;

            foreach ($this->currentBulkWriteIds as $id) {
                $this->existingIds[] = $id;
            }

            $this->currentBulkWriteIds = [];
        }
    }

    public function __destruct()
    {
        $this->flush();
    }

    protected function validateObject(Blackbox_Epace_Model_Epace_AbstractObject $object)
    {
        if ($this->epaceResource->getObjectType() != $object->getObjectType()) {
            throw new \Exception('Trying add to collection of type ' . $this->epaceResource->getObjectType() . ' object of type ' . $object->getObjectType());
        }
    }
}

class EpaceMongo extends Mage_Shell_Abstract
{
    /**
     * @var MongoDB\Driver\Manager;
     */
    protected $manager;
    protected $database;

    protected $collectionAdapters = [];

    protected $tabs = 0;

    public function run()
    {
        error_reporting(E_ALL);

        $host = $this->getArg('host');
        $this->manager = new MongoDB\Driver\Manager($host);

        $this->database = $this->getArg('database');
        if (!$this->database) {
            throw new \Exception('No database specified.');
        }

        $this->importToMongo();
    }

    public function importToMongo()
    {
        $from = $this->getArg('from');
        $to = $this->getArg('to');

        if ($this->getArg('global')) {
            $this->importEntities('salesPerson');
            $this->importEntities('salesCategory');
            $this->importEntities('salesTax');
            $this->importEntities('cSR');
            $this->importEntities('ship_provider');
            $this->importEntities('ship_via');
            $this->importEntities('country');
            $this->importEntities('estimate_status');
            $this->importEntities('job_status');
            $this->importEntities('job_type');
        }

        if ($this->getArg('estimates')) {
            /** @var Blackbox_Epace_Model_Resource_Epace_Estimate_Collection $collection */
            $collection = Mage::getResourceModel('efi/estimate_collection');
            if ($from) {
                $collection->addFilter('entryDate', ['gteq' => new DateTime($from)]);
            }
            if ($to) {
                $collection->addFilter('entryDate', ['lteq' => new DateTime($to)]);
            }

            if ($this->getArg('ef')) {
                $filters = json_decode($this->getArg('ef'));
                if (is_null($filters)) {
                    throw new \Exception("Invalid job filter");
                }
                if (!is_array($filters)) {
                    $filters = [$filters];
                }
                foreach ($filters as $filter) {
                    if (is_object($filter->value)) {
                        $filter->value = (array)$filter->value;
                    }
                    $collection->addFilter($filter->field, $filter->value);
                }
            }

            $ids = $collection->loadIds();
            $count = count($ids);
            $i = 0;
            $this->writeln('Found ' . $count . ' estimates.');
            foreach ($ids as $estimateId) {
                $this->writeln('Estimate ' . ++$i . '/' . $count . ': ' . $estimateId);
                /** @var Blackbox_Epace_Model_Epace_Estimate $estimate */
                $estimate = Mage::getModel('efi/estimate')->load($estimateId);
                $this->importEstimate($estimate);
            }
        }

        if ($this->getArg('jobs')) {
            /** @var Blackbox_Epace_Model_Resource_Epace_Job_Collection $collection */
            $collection = Mage::getResourceModel('efi/job_collection');
            if ($from) {
                $collection->addFilter('dateSetup', ['gteq' => new DateTime($from)]);
            }
            if ($to) {
                $collection->addFilter('dateSetup', ['lteq' => new DateTime($to)]);
            }

            if ($this->getArg('jf')) {
                $filters = json_decode($this->getArg('jf'));
                if (is_null($filters)) {
                    throw new \Exception("Invalid job filter");
                }
                if (!is_array($filters)) {
                    $filters = [$filters];
                }
                foreach ($filters as $filter) {
                    if (is_object($filter->value)) {
                        $filter->value = (array)$filter->value;
                    }
                    $collection->addFilter($filter->field, $filter->value);
                }
            }

            /** @var Mage_Core_Model_Resource $resource */
            $resource = Mage::getSingleton('core/resource');
            $connection = $resource->getConnection('core_read');
            $orderTable = $resource->getTableName('sales/order');

            $ids = $collection->loadIds();
            $count = count($ids);
            $i = 0;
            $this->writeln('Found ' . $count . ' jobs.');
            $this->tabs++;
            try {
                foreach ($ids as $jobId) {
                    $this->writeln('Job ' . ++$i . '/' . $count . ': ' . $jobId);
                    $select = $connection->select()->from($orderTable, 'count(*)')
                        ->where('epace_job_id = ?', $jobId);
                    if ($connection->fetchOne($select) > 0) {
                        $this->writeln("\tJob $jobId already imported.");
                    } else {
                        /** @var Blackbox_Epace_Model_Epace_Job $job */
                        $job = Mage::getModel('efi/job')->load($jobId);

                        if ($job->getEstimate()) {
                            $this->importEstimate($job->getEstimate());
                        } else {
                            $this->importJob($job);
                        }
                    }
                }
            } finally {
                $this->tabs--;
            }
        }
    }

    protected function importEstimate(Blackbox_Epace_Model_Epace_Estimate $estimate)
    {
        $this->getCollectionAdapter('estimate')->insertOrUpdate($estimate);
        foreach ($estimate->getProducts() as $product) {
            $this->getCollectionAdapter('estimate_product')->insertOrUpdate($product);
            foreach ($product->getParts() as $part) {
                $this->getCollectionAdapter('estimate_part')->insertOrUpdate($part);
                foreach ($part->getSizeAllowances() as $sizeAllowance) {
                    $this->getCollectionAdapter('estimate_part_sizeAllowance')->insertOrUpdate($sizeAllowance);
                }
                foreach ($part->getQuantities() as $quantity) {
                    $this->getCollectionAdapter('estimate_quantity')->insertOrUpdate($quantity);
                }
            }
            foreach ($product->getPriceSummaries() as $priceSummary) {
                $this->getCollectionAdapter('estimate_product_priceSummary')->insertOrUpdate($priceSummary);
            }
        }
        foreach ($estimate->getQuoteLetters() as $quoteLetter) {
            $this->getCollectionAdapter('estimate_quoteLetter')->insertOrUpdate($quoteLetter);
            foreach ($quoteLetter->getNotes() as $note) {
                $this->getCollectionAdapter('estimate_quoteLetter_note')->insertOrUpdate($note);
            }
        }
        if ($estimate->getCustomer()) {
            $this->getCollectionAdapter('customer')->insertIfNotExists($estimate->getCustomer());
        }
//        if ($estimate->getSalesPerson()) {
//            $this->getCollectionAdapter('salesPerson')->insertIfNotExists($estimate->getSalesPerson());
//        }
//        if ($estimate->getCSR()) {
//            $this->getCollectionAdapter('cSR')->insertIfNotExists($estimate->getCSR());
//        }
//        if ($estimate->getStatus()) {
//            $this->getCollectionAdapter('estimate_status')->insertIfNotExists($estimate->getStatus());
//        }

        if ($estimate->isConvertedToJob()) {
            $jobs = $estimate->getJobs();
            if (!empty ($jobs)) {
                $count = count($jobs);
                $this->writeln('Found ' . $count . ' jobs');
                $i = 0;
                $this->tabs++;
                try {
                    foreach ($estimate->getJobs() as $job) {
                        $this->writeln('Job ' . ++$i . '/' . $count . ': ' . $job->getId());
                        if ($job->getEstimateId() != $estimate->getId()) {
                            $this->writeln('Job source does match with estimate.');
                            continue;
                        }
                        $this->importJob($job);
                    }
                } finally {
                    $this->tabs--;
                }
            }
        }
    }

    protected function importJob(Blackbox_Epace_Model_Epace_Job $job)
    {
        $this->getCollectionAdapter('job')->insertOrUpdate($job);
//        if ($job->getAdminStatus()) {
//            $this->getCollectionAdapter('job_status')->insertIfNotExists($job->getAdminStatus());
//        }
//        if ($job->getPrevAdminStatus()) {
//            $this->getCollectionAdapter('job_status')->insertIfNotExists($job->getPrevAdminStatus());
//        }
        if ($job->getCustomer()) {
            $this->getCollectionAdapter('customer')->insertIfNotExists($job->getCustomer());
        }
//        if ($job->getCSR()) {
//            $this->getCollectionAdapter('cSR')->insertIfNotExists($job->getCSR());
//        }
//        if ($job->getSalesPerson()) {
//            $this->getCollectionAdapter('salesPerson')->insertIfNotExists($job->getSalesPerson());
//        }
        if ($job->getQuote()) {
            $this->getCollectionAdapter('quote')->insertIfNotExists($job->getQuote());
        }
        foreach($job->getProducts() as $product) {
            $this->getCollectionAdapter('job_product')->insertOrUpdate($product);
        }
//        $this->importShipVia($job->getShipVia());
        foreach ($job->getParts() as $part) {
            $this->getCollectionAdapter('job_part')->insertOrUpdate($part);
            foreach ($part->getMaterials() as $material) {
                $this->getCollectionAdapter('job_material')->insertOrUpdate($material);
            }
            foreach ($part->getPrePressOps() as $prePressOp) {
                $this->getCollectionAdapter('job_part_prePressOp')->insertOrUpdate($prePressOp);
            }
            foreach ($part->getChangeOrders() as $changeOrder) {
                $this->getCollectionAdapter('change_order')->insertOrUpdate($changeOrder);
            }
            foreach ($part->getProofs() as $proof) {
                $this->getCollectionAdapter('proof')->insertOrUpdate($proof);
            }
            foreach ($part->getItems() as $item) {
                $this->getCollectionAdapter('job_part_item')->insertOrUpdate($item);
            }
            foreach ($part->getPressForms() as $pressForm) {
                $this->getCollectionAdapter('job_part_pressForm')->insertOrUpdate($pressForm);
            }
            foreach ($part->getComponents() as $component) {
                $this->getCollectionAdapter('job_component')->insertOrUpdate($component);
            }
            foreach ($part->getFinishingOps() as $finishingOp) {
                $this->getCollectionAdapter('job_part_finishingOp')->insertOrUpdate($finishingOp);
            }
            foreach ($part->getOutsidePurchs() as $outsidePurch) {
                $this->getCollectionAdapter('job_part_outsidePurch')->insertOrUpdate($outsidePurch);
            }
            foreach ($part->getPlans() as $plan) {
                $this->getCollectionAdapter('job_plan')->insertOrUpdate($plan);
            }
            foreach ($part->getCosts() as $cost) {
                $this->getCollectionAdapter('job_cost')->insertOrUpdate($cost);
            }
            foreach ($part->getSizeAllowances() as $sizeAllowance) {
                $this->getCollectionAdapter('job_part_sizeAllowance')->insertOrUpdate($sizeAllowance);
            }
        }
        foreach ($job->getJobContacts() as $jobContact) {
            $this->getCollectionAdapter('job_contact')->insertOrUpdate($jobContact);
            $this->importContact($jobContact->getContact());
        }
        foreach ($job->getNotes() as $note) {
            $this->getCollectionAdapter('job_note')->insertOrUpdate($note);
        }

        $this->tabs++;
        try {
            $i = 0;
            $count = count($job->getInvoices());
            foreach ($job->getInvoices() as $invoice) {
                $i++;
                $this->writeln("Invoice $i/$count: {$invoice->getId()}");
                try {
                    $this->tabs++;
                    $this->importInvoice($invoice);
                } catch (\Exception $e) {
                    $this->writeln('Error: ' . $e->getMessage());
                } finally {
                    $this->tabs--;
                }
            }

            $i = 0;
            $count = count($job->getShipments());
            foreach ($job->getShipments() as $shipment) {
                $i++;
                $this->writeln("Shipment $i/$count: {$shipment->getId()}");
                try {
                    $this->tabs++;
                    $this->importShipment($shipment);
                } catch (\Exception $e) {
                    $this->writeln('Error: ' . $e->getMessage());
                } finally {
                    $this->tabs--;
                }
            }
        } finally {
            $this->tabs--;
        }
    }

    protected function importShipment(Blackbox_Epace_Model_Epace_Job_Shipment $jobShipment)
    {
        $this->getCollectionAdapter('job_shipment')->insertOrUpdate($jobShipment);
        $this->importContact($jobShipment->getContact());
        $this->importContact($jobShipment->getShipTo());
//        $this->importShipVia($jobShipment->getShipVia());
        foreach ($jobShipment->getCartons() as $carton) {
            $this->getCollectionAdapter('carton')->insertOrUpdate($carton);
            foreach ($carton->getContents() as $content) {
                $this->getCollectionAdapter('carton_content')->insertOrUpdate($content);
            }
        }
        foreach ($jobShipment->getSkids() as $skid) {
            $this->getCollectionAdapter('skid')->insertOrUpdate($skid);
        }
    }

    protected function importInvoice(Blackbox_Epace_Model_Epace_Invoice $invoice)
    {
        $this->getCollectionAdapter('invoice')->insertOrUpdate($invoice);
//        if ($invoice->getSalesCategory()) {
//            $this->getCollectionAdapter('salesCategory')->insertIfNotExists($invoice->getSalesCategory());
//        }
//        if ($invoice->getSalesTax()) {
//            $this->getCollectionAdapter('salesTax')->insertIfNotExists($invoice->getSalesTax());
//        }
        foreach ($invoice->getLines() as $line) {
            $this->getCollectionAdapter('invoice_line')->insertOrUpdate($line);
        }
        foreach ($invoice->getTaxDists() as $taxDist) {
            $this->getCollectionAdapter('invoice_taxDist')->insertOrUpdate($taxDist);
        }
        foreach ($invoice->getCommDists() as $commDist) {
            $this->getCollectionAdapter('invoice_commDist')->insertOrUpdate($commDist);
        }
        foreach ($invoice->getExtras() as $extra) {
            $this->getCollectionAdapter('invoice_extra')->insertOrUpdate($extra);
        }
        foreach ($invoice->getSalesDists() as $salesDist) {
            $this->getCollectionAdapter('invoice_salesDist')->insertOrUpdate($salesDist);
        }

        if ($invoice->getReceivable()) {
            $this->importReceivable($invoice->getReceivable());
        }
    }

    protected function importReceivable(Blackbox_Epace_Model_Epace_Receivable $receivable)
    {
        $this->getCollectionAdapter('receivable')->insertOrUpdate($receivable);
        foreach ($receivable->getLines() as $line) {
            $this->getCollectionAdapter('receivable_line')->insertOrUpdate($line);
        }
    }

    protected function importCustomer($customer)
    {
        if ($customer instanceof Blackbox_Epace_Model_Epace_Customer) {
            $this->getCollectionAdapter('customer')->insertIfNotExists($customer);
//            if ($customer->getSalesPerson()) {
//                $this->getCollectionAdapter('salesPerson')->insertIfNotExists($customer->getSalesPerson());
//            }
//            if ($customer->getSalesTax()) {
//                $this->getCollectionAdapter('salesTax')->insertIfNotExists($customer->getSalesTax());
//            }
//            if ($customer->getCSR()) {
//                $this->getCollectionAdapter('cSR')->insertIfNotExists($customer->getCSR());
//            }
//            if ($customer->getCountry()) {
//                $this->getCollectionAdapter('country')->insertIfNotExists($customer->getCountry());
//            }
//            if ($customer->getSalesCategory()) {
//                $this->getCollectionAdapter('salesCategory')->insertIfNotExists($customer->getSalesCategory());
//            }
        }
    }

    protected function importEntities($type)
    {
        /** @var Blackbox_Epace_Model_Resource_Epace_Collection $collection */
        $collection = Mage::getResourceModel('efi/' . $type . '_collection');
        $adapter = $this->getCollectionAdapter($type);
        foreach ($collection->getItems() as $item) {
            $adapter->insertOrUpdate($item);
        }
    }

    protected function importContact($contact)
    {
        if ($contact instanceof Blackbox_Epace_Model_Epace_Contact) {
            $this->getCollectionAdapter('contact')->insertIfNotExists($contact);
        }
    }

//    protected function importShipVia($shipVia)
//    {
//        if ($shipVia instanceof Blackbox_Epace_Model_Epace_Ship_Via) {
//            $this->getCollectionAdapter('ship_via')->insertIfNotExists($shipVia);
//            if ($shipVia->getShipProvider()) {
//                $this->getCollectionAdapter('ship_provider')->insertIfNotExists($shipVia->getShipProvider());
//            }
//        }
//    }

    /**
     * @param $class
     * @return MongoEpaceCollection
     */
    protected function getCollectionAdapter($class)
    {
        if (!$this->collectionAdapters[$class]) {
            $this->collectionAdapters[$class] = new MongoEpaceCollection(Mage::getModel('efi/' . $class), $this->manager, $this->database);
        }

        return $this->collectionAdapters[$class];
    }

    protected function writeln($message)
    {
        echo str_repeat("\t", $this->tabs) . $message . PHP_EOL;
    }

//    protected function checkDatabase($database)
//    {
//        $result = $this->manager->executeCommand('admin', new MongoDB\Driver\Command(['listDatabases' => 1]));
//        $databases = $result->toArray()[0];
//
//        foreach ($databases as $_database) {
//            if ($_database->name == $database) {
//                return true;
//            }
//        }
//
//        return false;
//    }
}

$shell = new EpaceMongo();
$shell->run();