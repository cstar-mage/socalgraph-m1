<?php

require_once 'abstract.php';

class EpaceMongoDebug
{
    public static $debug = false;
    public static $time = null;
}

EpaceMongoDebug::$time = time();

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
     * @var Blackbox_Epace_Helper_Mongo
     */
    protected $api;

    protected $debugInserts = [];

    /**
     * @var MongoDB\Driver\BulkWrite
     */
    protected $bulkWrite;
    protected $currentBulkWriteIds = [];

    public static $bulkWriteLimit = 500;

    protected $existingIds = [];

    protected $type;

    public function __construct($epaceResource, $manager, $database)
    {
        $this->epaceResource = $epaceResource;
        $this->manager = $manager;
        $this->database = $database;
        $this->type = Mage::helper('epace')->getTypeName($this->getCollectionName());
    }

    /**
     * @return string
     */
    public function getCollectionName()
    {
        return $this->epaceResource->getObjectType();
    }

    /**
     * @return string
     */
    public function getPrimariKey()
    {
        return $this->epaceResource->getIdFieldName();
    }

    /**
     * @return Blackbox_Epace_Model_Epace_AbstractObject
     */
    public function getResource()
    {
        return $this->epaceResource;
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
            'projection' => ['_id' => 1]
        ];

        $query = new \MongoDB\Driver\Query($filter, $options);
        $rows = $this->manager->executeQuery($this->database . '.' . $this->getCollectionName(), $query);

        $result = count($rows->toArray()) > 0;
        if ($result) {
            $this->existingIds[] = $id;
        }

        return $result;
    }

    public function loadData($id)
    {
        $query = new MongoDB\Driver\Query([
            '_id' => $id
        ]);
        $rows = $this->manager->executeQuery($this->database . '.' . $this->getCollectionName(), $query);
        foreach ($rows as $row) {
            return (array)$row;
        }
        return null;
    }

    public function loadIds($filter = [])
    {
        $options = [
            'projection' => ['_id' => 1]
        ];
        $rows = $this->manager->executeQuery($this->database . '.' . $this->getCollectionName(), new MongoDB\Driver\Query($filter, $options))->toArray();

        $result = [];
        foreach ($rows as $row) {
            $result[] = $row->_id;
        }

        return $result;
    }

    public function insertOrUpdate(Blackbox_Epace_Model_Epace_AbstractObject $object, $forceUpdate = false)
    {
        $this->validateObject($object);
        return $this->insertOrUpdateData($object->getData(), $forceUpdate);
    }

    protected function insertOrUpdateData($data, $forceUpdate = false)
    {
        $id = $data[$this->epaceResource->getIdFieldName()];
        if (in_array($id, $this->currentBulkWriteIds)) {
            if ($forceUpdate) {
                $this->flush();
            } else {
                return true;
            }
        }

        if (!$this->bulkWrite) {
            $this->bulkWrite = new MongoDB\Driver\BulkWrite(['ordered' => true]);
        }

        $data = $this->_prepareData($data, true);
        if ($old = $this->loadData($id)) {
            $systemFields = [
                '_created_at',
                '_updated_at',
                '_id'
            ];
            foreach ($systemFields as $field) {
                unset($old[$field]);
            }

            $update = false;
            foreach ($data as $key => $value) {
                if (in_array($key, $systemFields)) {
                    continue;
                }
                if ($value != $old[$key]) {
                    $update = true;
                    break;
                } else {
                    unset($old[$key]);
                }
            }
            if (!$update && empty($old)) {
                if ($forceUpdate) {
                    if (EpaceMongoDebug::$debug) {
                        echo $this->getCollectionName() . ' FORCE UPDATE' . PHP_EOL;
                    }
                    $this->bulkWrite->update(
                        ['_id' => $id],
                        ['$set' => ['_updated_at' => new MongoDB\BSON\UTCDateTime(time() * 1000)]],
                        ['multi' => false]);
                } else {
                    if (EpaceMongoDebug::$debug) {
                        echo $this->getCollectionName() . ' IGNORE' . PHP_EOL;
                    }
                    return false;
                }
            }
            $data['_updated_at'] = new MongoDB\BSON\UTCDateTime(time() * 1000);
            $this->bulkWrite->update(
                ['_id' => $id],
                ['$set' => $data],
                ['multi' => false]
            );
            if (EpaceMongoDebug::$debug) {
                echo $this->getCollectionName() . ' UPDATE' . PHP_EOL;
            }
        } else {
            $data['_created_at'] = new MongoDB\BSON\UTCDateTime(time() * 1000);
            $data['_updated_at'] = new MongoDB\BSON\UTCDateTime(time() * 1000);
            $this->bulkWrite->insert($data);
            $this->debugInserts[$id] = $data;
            if (EpaceMongoDebug::$debug) {
                echo $this->getCollectionName() . ' INSERT' . PHP_EOL;
            }
        }
        $this->currentBulkWriteIds[] = $id;

        if ($this->bulkWrite->count() >= self::$bulkWriteLimit) {
            $this->flush();
        }

        return true;
    }

    public function updateDataRaw($data)
    {
        $id = $data[$this->epaceResource->getIdFieldName()];
        if (in_array($id, $this->currentBulkWriteIds)) {
            return;
        }

        if (!$this->bulkWrite) {
            $this->bulkWrite = new MongoDB\Driver\BulkWrite(['ordered' => true]);
        }

        $this->bulkWrite->update(
            ['_id' => $id],
            ['$set' => $data],
            ['multi' => false]
        );
        $this->currentBulkWriteIds[] = $id;

        if ($this->bulkWrite->count() >= self::$bulkWriteLimit) {
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

            foreach ($this->debugInserts as $id => $data) {
                $entryDate = $data['entryDate'];
                $entryTime = $data['entryTime'];
                if ($entryDate instanceof \MongoDB\BSON\UTCDateTime && $entryTime instanceof \MongoDB\BSON\UTCDateTime) {
                    $dateFormatted = $entryDate->toDateTime()->format('Y-m-d H:i:s');
                    $timeFormatted = $entryTime->toDateTime()->format('Y-m-d H:i:s');
                    Mage::log($this->getCollectionName() . ' ' . $id . ' ' . $dateFormatted . ' ' . $timeFormatted, null, EpaceMongoDebug::$time . '_mongoInsertBefore.log', true);
                    if ($dateFormatted == $timeFormatted) {
                        $object = Mage::getModel('efi/' . $this->type)->load($id);
                        Mage::log($this->getCollectionName() . ' ' . $id . ' ' . print_r($object->getData(), true), null, EpaceMongoDebug::$time . '_mongoInsertBeforeMATCH.log', true);
                    }

                    $loadedData = $this->loadData($id);
                    /** @var \MongoDB\BSON\UTCDateTime $loadedDate */
                    $loadedDate = $loadedData['entryDate'];
                    /** @var \MongoDB\BSON\UTCDateTime $loadedTime */
                    $loadedTime = $loadedData['entryTime'];
                    $loadedDateFormatted = $loadedDate->toDateTime()->format('Y-m-d H:i:s');
                    $loadedTimeFormatted = $loadedTime->toDateTime()->format('Y-m-d H:i:s');
                    Mage::log($this->getCollectionName() . ' ' . $id . ' ' . $loadedDateFormatted . ' ' . $loadedTimeFormatted, null, EpaceMongoDebug::$time . '_mongoInsertAfter.log', true);
                    if ($loadedDateFormatted == $loadedTimeFormatted) {
                        if (!isset($object)) {
                            $object = Mage::getModel('efi/' . $this->type)->load($id);
                        }
                        Mage::log($this->getCollectionName() . ' ' . $id . ' ' . print_r($object->getData(), true), null, EpaceMongoDebug::$time . '_mongoInsertAfterMATCH.log', true);
                    }
                    if ($dateFormatted != $loadedDateFormatted || $timeFormatted != $loadedTimeFormatted) {
                        if (!isset($object)) {
                            $object = Mage::getModel('efi/' . $this->type)->load($id);
                        }
                        Mage::log($this->getCollectionName() . ' ' . $id . ' ' . print_r($object->getData(), true), null, EpaceMongoDebug::$time . '_mongoInsertNOTMATCH.log', true);
                    }
                }
            }
            $this->debugInserts = [];
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

    /**
     * @param array $data
     * @param bool $addId
     * @return array
     */
    protected function _prepareData($data, $addId)
    {
        foreach ($this->epaceResource->getDefinition() as $field => $type) {
            if (!array_key_exists($field, $data) || is_null($data[$field])) {
                continue;
            }
            if ($type == 'date') {
                if (is_string($data[$field]) && !is_numeric($data[$field])) {
                    $data[$field] = new MongoDB\BSON\UTCDateTime(strtotime($data[$field]) * 1000);
                } else {
                    $data[$field] = new MongoDB\BSON\UTCDateTime((int)$data[$field] * 1000);
                }
            }
        }
        if ($addId) {
            $data['_id'] = $data[$this->epaceResource->getIdFieldName()];
        }
        return $data;
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

    protected $processedEstimates = [];
    protected $processedJobs = [];

    public static $debug = false;

    /**
     * @var Blackbox_EpaceImport_Helper_Data
     */
    protected $helper;

    public function __construct()
    {
        parent::__construct();
        $this->helper = Mage::helper('epacei');
    }

    public function run()
    {
        error_reporting(E_ALL);
        if ($this->getArg('debug')) {
            EpaceMongoDebug::$debug = true;
        }

        $this->saveStatus('running');

        try {
            $host = $this->getArg('host');
            $this->manager = new MongoDB\Driver\Manager($host);

            $this->database = $this->getArg('database');
            if (!$this->database) {
                throw new \Exception('No database specified.');
            }

            if ($this->getArg('bulkWriteLimit')) {
                MongoEpaceCollection::$bulkWriteLimit = (int)$this->getArg('bulkWriteLimit');
            }

            if ($this->getArg('fixDates')) {
                $this->fixDates();
                return;
            }

            if ($this->getArg('resave')) {
                $this->resaveEntities();
                return;
            }

            $this->importToMongo();

            $this->saveStatus('success');
        } catch (\Exception $e) {
            Mage::logException($e);
            $this->saveStatus('error', 'Exception in ' . $e->getFile() . ':' . $e->getLine() . '. Message: ' . $e->getMessage());
        }
    }

    protected function saveStatus($status, $message = '')
    {
        if ($this->getArg('key')) {
            Mage::getConfig()->saveConfig('/epace_import/mongo/' . $this->getArg('key'), json_encode([
                'time' => time(),
                'status' => $status,
                'message' => $message
            ]));
        }
    }

    public function importToMongo()
    {
        $from = $this->getArg('from');
        $to = $this->getArg('to');
        if ($to) {
            $to = new \DateTime($to);
        }
//        $to = false; // operators < and <= do not work

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
            $this->importEntities('invoice_extra_type');
        }

        if ($this->getArg('estimates')) {
            /** @var Blackbox_Epace_Model_Resource_Epace_Estimate_Collection $collection */
            $collection = Mage::getResourceModel('efi/estimate_collection');
            if ($from) {
                $collection->addFilter('entryDate', ['gteq' => new DateTime($from)]);
            }
            // operators < and <= do not work
//            if ($to) {
//                $collection->addFilter('entryDate', ['lteq' => new DateTime($to)]);
//            }
            if ($to) {
                $collection->setOrder('entryDate', 'ASC');
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
                if ($to && $this->helper->getTimestamp($estimate->getEntryDate(), $estimate->getEntryTime()) > $to->getTimestamp()) {
                    $this->writeln("Skip $estimateId because of --to arg: " . $estimate->getEntryDate() . ' ' . $estimate->getEntryTime());
                    break;
                }
                $this->importEstimate($estimate);
            }
        }

        if ($this->getArg('jobs')) {
            /** @var Blackbox_Epace_Model_Resource_Epace_Job_Collection $collection */
            $collection = Mage::getResourceModel('efi/job_collection');
            if ($from) {
                $collection->addFilter('dateSetup', ['gteq' => new DateTime($from)]);
            }
//            if ($to) {
//                $collection->addFilter('dateSetup', ['lteq' => new DateTime($to)]);
//            }
            if ($to) {
                $collection->setOrder('dateSetup', 'ASC');
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

            $ids = $collection->loadIds();
            $count = count($ids);
            $i = 0;
            $this->writeln('Found ' . $count . ' jobs.');
            $this->tabs++;
            try {
                foreach ($ids as $jobId) {
                    $this->writeln('Job ' . ++$i . '/' . $count . ': ' . $jobId);
                    if (in_array($jobId, $this->processedJobs)) {
                        $this->writeln("\tJob $jobId already processed.");
                    } else {
                        /** @var Blackbox_Epace_Model_Epace_Job $job */
                        $job = Mage::getModel('efi/job')->load($jobId);
                        if ($to && $this->helper->getTimestamp($job->getDateSetup(), $job->getTimeSetUp()) > $to->getTimestamp()) {
                            $this->writeln("\tSkip $jobId because of --to arg: " . $job->getDateSetup() . ' ' . $job->getTimeSetUp());
                            break;
                        }

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

    public function resaveEntities()
    {
        Blackbox_Epace_Model_Epace_AbstractObject::$useMongo = true;

        $entities = array_filter(explode(',', $this->getArg('resave')));
        if (empty($entities)) {
            $this->writeln('Error: entities are empty.');
            return;
        }

        foreach ($entities as $entity) {
            $adapter = $this->getCollectionAdapter($entity);
            /** @var Blackbox_Epace_Model_Resource_Epace_Collection $collection */
            $collection = Mage::getResourceModel('efi/' . $entity . '_collection');
            if (!$adapter || !$collection) {
                $this->writeln('Error: invalid entity ' . $entity);
            }
            $this->writeln($adapter->getCollectionName());

            $ids = $collection->loadIds();
            foreach ($ids as $id) {
                $this->writeln($id);
                $object = Mage::getModel('efi/' . $entity)->load($id);
                $adapter->updateDataRaw($object->getData());
            }
        }

        $this->writeln('Success.');
    }

    protected function fixDates()
    {
        $collections = $this->manager->executeCommand($this->database, new MongoDB\Driver\Command(['listCollections' => 1, 'nameOnly' => true]));
        /** @var Blackbox_Epace_Helper_Data $helper */
        $helper = Mage::helper('epace');
        foreach ($collections as $collection) {
            $type = $helper->getTypeName($collection->name);
            if ($type) {
                $this->writeln($collection->name);
                $this->fixCollectionDates($type);
            } else {
                $this->writeln('Object type for collection ' . $collection->name . ' not found');
            }
        }
    }

    protected function fixCollectionDates($type)
    {
        Blackbox_Epace_Model_Epace_AbstractObject::$useMongo = true;
        $adapter = $this->getCollectionAdapter($type);

        $definition = $adapter->getResource()->getDefinition();
        $dateFields = array_keys(array_filter($definition, function($value) {
            return $value == 'date';
        }));

        $ids = $adapter->loadIds();
        $count = count($ids);
        $i = 0;
        foreach ($ids as $id) {
            $this->write(++$i . '/' . $count . ' ' . $id);
            $data = $adapter->loadData($id);
            if (!$data) {
                $this->writeln('not found');
                continue;
            }

            $updated = false;
            foreach ($dateFields as $field) {
                if (!array_key_exists($field, $data) || is_null($data[$field]) || $data[$field] instanceof MongoDB\BSON\UTCDateTime) {
                    continue;
                }

                if (is_string($data[$field]) && !is_numeric($data[$field])) {
                    $data[$field] = new MongoDB\BSON\UTCDateTime(strtotime($data[$field]) *1000);
                } else {
                    $data[$field] = new MongoDB\BSON\UTCDateTime((int)$data[$field] * 1000);
                }
                $updated = true;
            }

            if (!isset($data['_created_at'])) {
                $data['_created_at'] = new MongoDB\BSON\UTCDateTime(time() * 1000);
                $data['_updated_at'] = $data['_created_at'];
                $updated = true;
            } else if (!isset($data['_updated_at'])) {
                $data['_updated_at'] = $data['_created_at'];
                $updated = true;
            }

            if ($updated) {
                $this->writeln(' update');
                $adapter->updateDataRaw($data);
            } else {
                $this->writeln('');
            }
        }

        $adapter->flush();
    }

    protected function importEstimate(Blackbox_Epace_Model_Epace_Estimate $estimate)
    {
        if (in_array($estimate->getId(), $this->processedEstimates)) {
            $this->writeln('Estimate ealready processed');
        } else {
            $forceUpdate = false;
            foreach ($estimate->getProducts() as $product) {
                $forceUpdate |= $this->getCollectionAdapter('estimate_product')->insertOrUpdate($product);
                foreach ($product->getParts() as $part) {
                    $forceUpdate |= $this->getCollectionAdapter('estimate_part')->insertOrUpdate($part);
                    foreach ($part->getSizeAllowances() as $sizeAllowance) {
                        $forceUpdate |= $this->getCollectionAdapter('estimate_part_sizeAllowance')->insertOrUpdate($sizeAllowance);
                    }
                    foreach ($part->getQuantities() as $quantity) {
                        $forceUpdate |= $this->getCollectionAdapter('estimate_quantity')->insertOrUpdate($quantity);
                    }
                }
                foreach ($product->getPriceSummaries() as $priceSummary) {
                    $forceUpdate |= $this->getCollectionAdapter('estimate_product_priceSummary')->insertOrUpdate($priceSummary);
                }
            }
            foreach ($estimate->getQuoteLetters() as $quoteLetter) {
                $forceUpdate |= $this->getCollectionAdapter('estimate_quoteLetter')->insertOrUpdate($quoteLetter);
                foreach ($quoteLetter->getNotes() as $note) {
                    $forceUpdate |= $this->getCollectionAdapter('estimate_quoteLetter_note')->insertOrUpdate($note);
                }
            }
            if ($estimate->getCustomer()) {
                $this->getCollectionAdapter('customer')->insertOrUpdate($estimate->getCustomer());
            }

            if (!$estimate->getEntryDate() || !$estimate->getEntryTime()) {
                $this->writeln('[DEBUG] Estimate entry date or time is empty. ' . print_r($estimate->getData(), true));
            } else {
                $yearAgo = strtotime('-1 year');
                if (strtotime($estimate->getEntryDate()) + strtotime($estimate->getEntryTime()) < $yearAgo) {
                    $this->writeln('[DEBUG] Estimate entry datetime is earlier then a year. ' . print_r($estimate->getData(), true));
                }
            }

            $this->getCollectionAdapter('estimate')->insertOrUpdate($estimate, $forceUpdate);

            $this->processedEstimates[] = $estimate->getId();
        }
//        if ($estimate->getSalesPerson()) {
//            $this->getCollectionAdapter('salesPerson')->insertOrUpdate($estimate->getSalesPerson());
//        }
//        if ($estimate->getCSR()) {
//            $this->getCollectionAdapter('cSR')->insertOrUpdate($estimate->getCSR());
//        }
//        if ($estimate->getStatus()) {
//            $this->getCollectionAdapter('estimate_status')->insertOrUpdate($estimate->getStatus());
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
        if (in_array($job->getId(), $this->processedJobs)) {
            $this->writeln('Job already processed');
            return;
        }
        $forceUpdate = false;
//        if ($job->getAdminStatus()) {
//            $this->getCollectionAdapter('job_status')->insertOrUpdate($job->getAdminStatus());
//        }
//        if ($job->getPrevAdminStatus()) {
//            $this->getCollectionAdapter('job_status')->insertOrUpdate($job->getPrevAdminStatus());
//        }
        if ($job->getCustomer()) {
            $this->getCollectionAdapter('customer')->insertOrUpdate($job->getCustomer());
        }
//        if ($job->getCSR()) {
//            $this->getCollectionAdapter('cSR')->insertOrUpdate($job->getCSR());
//        }
//        if ($job->getSalesPerson()) {
//            $this->getCollectionAdapter('salesPerson')->insertOrUpdate($job->getSalesPerson());
//        }
        if ($job->getQuote()) {
            $this->getCollectionAdapter('quote')->insertOrUpdate($job->getQuote());
        }
        foreach($job->getProducts() as $product) {
            $forceUpdate |= $this->getCollectionAdapter('job_product')->insertOrUpdate($product);
        }
//        $this->importShipVia($job->getShipVia());
        foreach ($job->getParts() as $part) {
            $forceUpdate |= $this->getCollectionAdapter('job_part')->insertOrUpdate($part);
            foreach ($part->getMaterials() as $material) {
                $forceUpdate |= $this->getCollectionAdapter('job_material')->insertOrUpdate($material);
            }
            foreach ($part->getPrePressOps() as $prePressOp) {
                $forceUpdate |= $this->getCollectionAdapter('job_part_prePressOp')->insertOrUpdate($prePressOp);
            }
            foreach ($part->getChangeOrders() as $changeOrder) {
                $forceUpdate |= $this->getCollectionAdapter('change_order')->insertOrUpdate($changeOrder);
            }
            foreach ($part->getProofs() as $proof) {
                $forceUpdate |= $this->getCollectionAdapter('proof')->insertOrUpdate($proof);
            }
            foreach ($part->getItems() as $item) {
                $forceUpdate |= $this->getCollectionAdapter('job_part_item')->insertOrUpdate($item);
            }
            foreach ($part->getPressForms() as $pressForm) {
                $forceUpdate |= $this->getCollectionAdapter('job_part_pressForm')->insertOrUpdate($pressForm);
            }
            foreach ($part->getComponents() as $component) {
                $forceUpdate |= $this->getCollectionAdapter('job_component')->insertOrUpdate($component);
            }
            foreach ($part->getFinishingOps() as $finishingOp) {
                $forceUpdate |= $this->getCollectionAdapter('job_part_finishingOp')->insertOrUpdate($finishingOp);
            }
            foreach ($part->getOutsidePurchs() as $outsidePurch) {
                $forceUpdate |= $this->getCollectionAdapter('job_part_outsidePurch')->insertOrUpdate($outsidePurch);
            }
            foreach ($part->getPlans() as $plan) {
                $forceUpdate |= $this->getCollectionAdapter('job_plan')->insertOrUpdate($plan);
            }
            foreach ($part->getCosts() as $cost) {
                $forceUpdate |= $this->getCollectionAdapter('job_cost')->insertOrUpdate($cost);
            }
            foreach ($part->getSizeAllowances() as $sizeAllowance) {
                $forceUpdate |= $this->getCollectionAdapter('job_part_sizeAllowance')->insertOrUpdate($sizeAllowance);
            }
        }
        foreach ($job->getJobContacts() as $jobContact) {
            $forceUpdate |= $this->getCollectionAdapter('job_contact')->insertOrUpdate($jobContact);
            $this->importContact($jobContact->getContact());
        }
        foreach ($job->getNotes() as $note) {
            $forceUpdate |= $this->getCollectionAdapter('job_note')->insertOrUpdate($note);
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
                    $forceUpdate |= $this->importInvoice($invoice);
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
                    $forceUpdate |= $this->importShipment($shipment);
                } catch (\Exception $e) {
                    $this->writeln('Error: ' . $e->getMessage());
                } finally {
                    $this->tabs--;
                }
            }
        } finally {
            $this->tabs--;
        }

        if (!$job->getDateSetup() || !$job->getTimeSetUp()) {
            $this->writeln('[DEBUG] Job setup date or time is empty. ' . print_r($job->getData(), true));
        } else {
            $yearAgo = strtotime('-1 year');
            if (strtotime($job->getDateSetup()) + strtotime($job->getTimeSetUp()) < $yearAgo) {
                $this->writeln('[DEBUG] Job setup datetime is earlier then a year. ' . print_r($job->getData(), true));
            }
        }

        $this->getCollectionAdapter('job')->insertOrUpdate($job, $forceUpdate);

        $this->processedJobs[] = $job->getId();
    }

    protected function importShipment(Blackbox_Epace_Model_Epace_Job_Shipment $jobShipment)
    {
        $forceUpdate = false;
        $forceUpdate |= $this->importContact($jobShipment->getContact());
        $forceUpdate |= $this->importContact($jobShipment->getShipTo());
//        $this->importShipVia($jobShipment->getShipVia());
        foreach ($jobShipment->getCartons() as $carton) {
            $forceUpdate |= $this->getCollectionAdapter('carton')->insertOrUpdate($carton);
            foreach ($carton->getContents() as $content) {
                $forceUpdate |= $this->getCollectionAdapter('carton_content')->insertOrUpdate($content);
            }
        }
        foreach ($jobShipment->getSkids() as $skid) {
            $forceUpdate |= $this->getCollectionAdapter('skid')->insertOrUpdate($skid);
        }

        $this->getCollectionAdapter('job_shipment')->insertOrUpdate($jobShipment, $forceUpdate);
    }

    protected function importInvoice(Blackbox_Epace_Model_Epace_Invoice $invoice)
    {
        $forceUpdate = false;
//        if ($invoice->getSalesCategory()) {
//            $this->getCollectionAdapter('salesCategory')->insertOrUpdate($invoice->getSalesCategory());
//        }
//        if ($invoice->getSalesTax()) {
//            $this->getCollectionAdapter('salesTax')->insertOrUpdate($invoice->getSalesTax());
//        }
        foreach ($invoice->getLines() as $line) {
            $forceUpdate |= $this->getCollectionAdapter('invoice_line')->insertOrUpdate($line);
        }
        foreach ($invoice->getTaxDists() as $taxDist) {
            $forceUpdate |= $this->getCollectionAdapter('invoice_taxDist')->insertOrUpdate($taxDist);
        }
        foreach ($invoice->getCommDists() as $commDist) {
            $forceUpdate |= $this->getCollectionAdapter('invoice_commDist')->insertOrUpdate($commDist);
        }
        foreach ($invoice->getExtras() as $extra) {
            $forceUpdate |= $this->getCollectionAdapter('invoice_extra')->insertOrUpdate($extra);
        }
        foreach ($invoice->getSalesDists() as $salesDist) {
            $forceUpdate |= $this->getCollectionAdapter('invoice_salesDist')->insertOrUpdate($salesDist);
        }

        if ($invoice->getReceivable()) {
            $forceUpdate |= $this->importReceivable($invoice->getReceivable());
        }

        return $this->getCollectionAdapter('invoice')->insertOrUpdate($invoice, $forceUpdate);
    }

    protected function importReceivable(Blackbox_Epace_Model_Epace_Receivable $receivable)
    {
        $forceUpdate = false;
        foreach ($receivable->getLines() as $line) {
            $forceUpdate |= $this->getCollectionAdapter('receivable_line')->insertOrUpdate($line);
        }

        return $this->getCollectionAdapter('receivable')->insertOrUpdate($receivable, $forceUpdate);
    }

    protected function importCustomer($customer)
    {
        if ($customer instanceof Blackbox_Epace_Model_Epace_Customer) {
            $this->getCollectionAdapter('customer')->insertOrUpdate($customer);
//            if ($customer->getSalesPerson()) {
//                $this->getCollectionAdapter('salesPerson')->insertOrUpdate($customer->getSalesPerson());
//            }
//            if ($customer->getSalesTax()) {
//                $this->getCollectionAdapter('salesTax')->insertOrUpdate($customer->getSalesTax());
//            }
//            if ($customer->getCSR()) {
//                $this->getCollectionAdapter('cSR')->insertOrUpdate($customer->getCSR());
//            }
//            if ($customer->getCountry()) {
//                $this->getCollectionAdapter('country')->insertOrUpdate($customer->getCountry());
//            }
//            if ($customer->getSalesCategory()) {
//                $this->getCollectionAdapter('salesCategory')->insertOrUpdate($customer->getSalesCategory());
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

        $adapter->flush();
    }

    protected function importContact($contact)
    {
        if ($contact instanceof Blackbox_Epace_Model_Epace_Contact) {
            return $this->getCollectionAdapter('contact')->insertOrUpdate($contact);
        }
        return false;
    }

//    protected function importShipVia($shipVia)
//    {
//        if ($shipVia instanceof Blackbox_Epace_Model_Epace_Ship_Via) {
//            $this->getCollectionAdapter('ship_via')->insertOrUpdate($shipVia);
//            if ($shipVia->getShipProvider()) {
//                $this->getCollectionAdapter('ship_provider')->insertOrUpdate($shipVia->getShipProvider());
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

    protected function write($message)
    {
        echo str_repeat("\t", $this->tabs) . $message;
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