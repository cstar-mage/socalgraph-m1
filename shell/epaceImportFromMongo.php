<?php

require_once 'abstract.php';

class Shell_EpaceImportFromMongo extends Mage_Shell_Abstract
{
    protected $entities = [
        'Estimate' => [
            'keys' => [
                'e',
                'estimates'
            ],
            'dateField' => 'entryDate',
            'magentoClass' => 'epacei/estimate',
            'idField' => 'epace_estimate_id',
            'importMethod' => 'importEstimate',
            'updateMethod' => 'updateEstimate'
        ],
        'Job' => [
            'keys' => [
                'j',
                'jobs'
            ],
            'dateField' => 'dateSetup',
            'magentoClass' => 'sales/order',
            'idField' => 'epace_job_id',
            'importMethod' => 'importJob',
            'updateMethod' => 'updateOrder'
        ],
        'Invoice' => [
            'keys' => [
                'i',
                'invoices'
            ],
            'dateField' => 'invoiceDate',
            'magentoClass' => 'sales/order_invoice',
            'idField' => 'epace_invoice_id',
            'importMethod' => 'importInvoice',
            'updateMethod' => 'updateInvoice'
        ],
        'Receivable' => [
            'keys' => [
                'r',
                'receivables'
            ],
            'dateField' => 'invoiceDate',
            'magentoClass' => 'epacei/receivable',
            'idField' => 'epace_receivable_id',
            'importMethod' => 'importReceivable',
            'updateMethod' => 'updateReceivable'
        ],
        'JobShipment' => [
            'keys' => [
                's',
                'shipments'
            ],
            'dateField' => 'date',
            'magentoClass' => 'sales/order_shipment',
            'idField' => 'epace_shipment_id',
            'importMethod' => 'importShipment',
            'updateMethod' => 'updateShipment'
        ],
        'PurchaseOrder' => [
            'keys' => [
                'po',
                'purchaseOrders'
            ],
            'dateField' => 'dateEntered',
            'magentoClass' => 'epacei/purchaseOrder',
            'idField' => 'epace_purchase_order_id',
            'importMethod' => 'importPurchaseOrder',
            'updateMethod' => 'updatePurchaseOrder'
        ]
    ];

    protected $processed = [];

    /**
     * @var Blackbox_Epace_Helper_Data
     */
    protected $epaceHelper;

    /**
     * @var Blackbox_EpaceImport_Helper_Data
     */
    protected $helper;

    protected $tabs = 0;
    protected $newLine = true;
    protected $logTime = false;

    public function __construct()
    {
        parent::__construct();

        $this->epaceHelper = Mage::helper('epace');
        $this->helper = Mage::helper('epacei');

        $this->helper->setOutput(function ($msg) {
            $this->writeln($msg);
        });
    }

    public function run()
    {
        Blackbox_Epace_Model_Epace_AbstractObject::$useMongo = true;

        if ($this->getArg('time')) {
            $this->logTime = true;
        }

        $update = $this->getArg('update');
        $from = $this->getArg('from');
        $to = $this->getArg('to');

        foreach ($this->entities as $entity => $settings) {
            $found = false;
            foreach ($settings['keys'] as $key) {
                if ($this->getArg($key)) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                continue;
            }

            if ($this->getArg('updateDeletedChildren')) {
                $this->updateDeletedChildren($entity);
            } else {
                $this->processEntities($entity, $from, $to, $update);
            }
        }

        $this->writeln('Done.');
    }

    protected function processEntities($name, $from, $to, $update)
    {
        $this->writeln('Processing entities ' . $name);

        $settings = $this->entities[$name];

        $epaceModelType = $this->epaceHelper->getTypeName($name);
        /** @var Blackbox_Epace_Model_Resource_Epace_Collection $epaceCollection */
        $epaceCollection = Mage::getResourceModel('efi/' . $epaceModelType . '_collection');
        if ($from) {
            $epaceCollection->addFilter($settings['dateField'], ['gteq' => new \DateTime($from)]);
        }
        if ($to) {
            $epaceCollection->addFilter($settings['dateField'], ['lteq' => new \DateTime($to)]);
        }

        if ($filter = $this->getArg($name . 'Filter')) {
            $this->addFilter($epaceCollection, $filter);
        }

        $ids = $epaceCollection->loadIds();
        $count = count($ids);

        if ($this->getArg('notImported')) {
            /** @var Mage_Core_Model_Resource $resource */
            $resource = Mage::getSingleton('core/resource');
            $connection = $resource->getConnection('core_read');

            $magentoModel = Mage::getModel($settings['magentoClass']);

            $select = $connection->select()->from(['main_table' => $magentoModel->getResource()->getMainTable()], $settings['idField'])
                ->where($settings['idField'] . ' IS NOT NULL');

            $importedIds = $connection->fetchCol($select);
            foreach ($ids as $key => $id) {
                if (in_array($id, $importedIds)) {
                    unset($ids[$key]);
                }
            }

            $oldCount = $count;
            $count = count($ids);
            $this->writeln('Found ' . $count . ' (' . $oldCount . ')');
        } else {
            $this->writeln('Found ' . $count . ' entries.');
        }
        $i = 0;

        $this->tabs++;
        try {
            foreach ($ids as $id) {
                $this->write(++$i . '/' . $count . ' ' . $id);

                if (in_array($id, $this->processed[$name])) {
                    $this->writeln(' Already processed.');
                    continue;
                }

                try {
                    $magentoModel = Mage::getModel($settings['magentoClass'])->load($id, $settings['idField']);
                    if ($magentoModel->getId()) {
                        if (!$update) {
                            $this->writeln(' Already imported: ' . $magentoModel->getId() . '.');
                            continue;
                        }
                        $this->writeln(' update');
                        $this->tabs++;
                        try {
                            call_user_func([$this->helper, $settings['updateMethod']], $magentoModel, true);
                        } finally {
                            $this->tabs--;
                        }
                        if ($magentoModel instanceof Mage_Sales_Model_Order_Shipment) {
                            $magentoModel->getOrder()->reset();
                            $magentoModel->dispose();
                            gc_collect_cycles();
                        }
                    } else {
                        $this->writeln(' create');
                        $this->tabs++;
                        try {
                            $epaceModel = Mage::getModel('efi/' . $epaceModelType)->load($id);
                            if (is_null($epaceModel->getid())) {
                                throw new \Exception('Unable to load entity from mongo.');
                            }
                            call_user_func([$this, $settings['importMethod']], $epaceModel);
                        } finally {
                            $this->tabs--;
                        }
                    }

                    $this->processed[$name][] = $id;
                } catch (\Exception $e) {
                    $this->writeln('Error: ' . $e->getMessage());
                }
            }
        } finally {
            $this->tabs--;
        }
    }

    public function importEstimate(Blackbox_Epace_Model_Epace_Estimate $estimate, $checkImported = false)
    {
        /** @var Blackbox_EpaceImport_Model_Estimate $magentoEstimate */
        $magentoEstimate = Mage::getModel('epacei/estimate');
        if ($checkImported) {
            $magentoEstimate->loadByAttribute('epace_estimate_id', $estimate->getId());
            if ($magentoEstimate->getId()) {
                $this->writeln('Estimate ' . $estimate->getId() . ' already imported.');
                return;
            }
        }

        $this->helper->importEstimate($estimate, $magentoEstimate);
        $magentoEstimate->save();

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
                        $this->importJob($job, $magentoEstimate, true);
                    }
                } finally {
                    $this->tabs--;
                }
            }
        }
    }

    public function importJob(Blackbox_Epace_Model_Epace_Job $job, Blackbox_EpaceImport_Model_Estimate $magentoEstimate = null, $checkImproted = false)
    {
        if (in_array($job->getId(), $this->processed['Job'])) {
            $this->writeln('Already processed.');
            return;
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order');

        if ($checkImproted) {
            $order->loadByAttribute('epace_job_id', $job->getId());
        }

        if ($order->getId()) {
            $this->writeln('Job ' . $job->getId() . ' already imported');
            return;
        }

        $this->helper->importJob($job, $order, $magentoEstimate);
        $order->save();

        $this->tabs++;
        try {
            $i = 0;
            $count = count($job->getInvoices());
            foreach ($job->getInvoices() as $invoice) {
                $i++;
                $this->writeln("Invoice $i/$count: {$invoice->getId()}");
                try {
                    $this->importInvoice($invoice, $order, true);
                } catch (\Exception $e) {
                    $this->writeln('Error: ' . $e->getMessage());
                }
            }

            $i = 0;
            $count = count($job->getShipments());
            foreach ($job->getShipments() as $shipment) {
                $i++;
                $this->writeln("Shipment $i/$count: {$shipment->getId()}");
                try {
                    $this->importShipment($shipment, $order, true);
                } catch (\Exception $e) {
                    $this->writeln('Error: ' . $e->getMessage());
                }
            }
        } finally {
            $this->tabs--;
        }
    }

    public function importShipment(Blackbox_Epace_Model_Epace_Job_Shipment $jobShipment, Mage_Sales_Model_Order $order = null, $checkImported = true)
    {
        if (in_array($jobShipment->getId(), $this->processed['JobShipment'])) {
            $this->writeln('Already processed.');
            return;
        }

        /** @var Mage_Sales_Model_Order_Shipment $orderShipment */
        $orderShipment = Mage::getModel('sales/order_shipment');

        if ($checkImported) {
            $orderShipment->load($jobShipment->getId(), 'epace_shipment_id');
            if ($orderShipment->getId()) {
                $this->writeln("Shipment {$jobShipment->getId()} already imported.");
                return;
            }
        }

        $this->helper->importShipment($jobShipment, $order, $orderShipment);
        $orderShipment->save();
    }

    public function importInvoice(Blackbox_Epace_Model_Epace_Invoice $invoice, Mage_Sales_Model_Order $order = null, $checkImported = true)
    {
        if (in_array($invoice->getId(), $this->processed['Invoice'])) {
            $this->writeln('Already processed.');
            return;
        }

        /** @var Mage_Sales_Model_Order_Invoice $magentoInvoice */
        $magentoInvoice = Mage::getModel('sales/order_invoice');
        if ($checkImported) {
            $magentoInvoice->load($invoice->getId(), 'epace_invoice_id');
            if ($magentoInvoice->getId()) {
                $this->writeln("Invoice {$invoice->getId()} already imported.");
                return;
            }
        }

        $this->helper->importInvoice($invoice, $order, $magentoInvoice);
        $magentoInvoice->save();

        if ($magentoInvoice->getEpaceReceivableId() && !in_array($magentoInvoice->getEpaceReceivableId(), $this->processed['Receivable'])) {
            $this->processed['Receivable'][] = $magentoInvoice->getEpaceReceivableId();
        }
//        if ($invoice->getReceivable()) {
//            $this->tabs++;
//            try {
//                $this->helper->importReceivable($invoice->getReceivable(), $magentoInvoice)->save();
//            } finally {
//                $this->tabs--;
//            }
//        }
    }

    public function importReceivable(Blackbox_Epace_Model_Epace_Receivable $receivable, Mage_Sales_Model_Order $order = null, $checkImported = true)
    {
        if (in_array($receivable->getId(), $this->processed['Receivable'])) {
            $this->writeln('Already processed.');
            return;
        }

        /** @var Blackbox_EpaceImport_Model_Receivable $magentoReceivable */
        $magentoReceivable = Mage::getModel('epacei/receivable');
        if ($checkImported) {
            $magentoReceivable->load($receivable->getId(), 'epace_receivable_id');
            if ($magentoReceivable->getId()) {
                $this->writeln("Receivable {$receivable->getId()} already imported.");
                return;
            }
        }

        $this->helper->importReceivable($receivable, $order, $magentoReceivable);
        $magentoReceivable->save();
    }

    public function importPurchaseOrder(Blackbox_Epace_Model_Epace_Purchase_Order $purchaseOrder, $checkImported = false)
    {
        if (in_array($purchaseOrder->getId(), $this->processed['PurchaseOrder'])) {
            $this->writeln('Already processed.');
            return;
        }

        /** @var Blackbox_EpaceImport_Model_PurchaseOrder $mpo */
        $mpo = Mage::getModel('epacei/purchaseOrder');
        if ($checkImported) {
            $mpo->load($purchaseOrder->getId(), 'epace_receivable_id');
            if ($mpo->getId()) {
                $this->writeln("PurchaseOrder {$purchaseOrder->getId()} already imported.");
                return;
            }
        }

        $this->helper->importPurchaseOrder($purchaseOrder, $mpo);
        $mpo->save();
    }

    protected function updateDeletedChildren($entity)
    {
        $method = 'updateDeleted' . $entity . 'Children';
        if (!method_exists($this, $method)) {
            throw new \Exception('Method ' . $method . ' do not exist.');
        }

        call_user_func([$this, $method]);
    }

    protected function updateDeletedEstimateChildren()
    {
        $ids = $this->helper->getDeletedIds('EstimateQuantity', 'epacei/estimate_item', 'epace_estimate_quantity_id');
        $this->writeln('Found ' . count($ids) . ' deleted estimate quantities.');
        if (empty($ids)) {
            return;
        }

        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_read');
        $select = $connection->select()->from($resource->getTableName('epacei/estimate_item'), 'estimate_id')
            ->where('epace_estimate_quantity_id IN (?)', $ids)
            ->group('estimate_id');

        /** @var Blackbox_EpaceImport_Model_Resource_Estimate_Collection $collection */
        $collection = Mage::getResourceModel('epacei/estimate_collection');
        $collection->getSelect()->where('entity_id IN (' . $select . ')');

        $count = count($collection->getItems());
        $this->writeln('Found ' . $count . ' estimates');
        $i = 0;

        foreach ($collection->getItems() as $estimate) {
            $this->writeln(++$i . '/' . $count . ' ' . $estimate->getId());
            $this->helper->updateEstimate($estimate, true);
        }
    }

    protected function updateDeletedInvoiceChildren()
    {
        $ids = $this->helper->getDeletedIds('InvoiceLine', 'sales/order_invoice_item', 'epace_invoice_line_id');
        $this->writeln('Found ' . count($ids) . ' deleted invoice lines.');
        if (empty($ids)) {
            return;
        }

        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_read');
        $select = $connection->select()->from($resource->getTableName('sales/invoice_item'), 'parent_id')
            ->where('epace_invoice_line_id IN (?)', $ids)
            ->group('parent_id');

        /** @var Mage_Sales_Model_Resource_Order_Invoice_Collection $collection */
        $collection = Mage::getResourceModel('sales/order_invoice_collection');
        $collection->getSelect()->where('entity_id IN (' . $select . ')');

        $count = count($collection->getItems());
        $this->writeln('Found ' . $count . ' invoices');
        $i = 0;

        foreach ($collection->getItems() as $invoice) {
            $this->writeln(++$i . '/' . $count . ' ' . $invoice->getId());
            $this->helper->updateInvoice($invoice, true);
        }
    }

    /**
     * @param Blackbox_Epace_Model_Resource_Epace_Collection $collection
     * @param string|array|object $filters
     * @throws Exception
     */
    protected function addFilter(Blackbox_Epace_Model_Resource_Epace_Collection $collection, $filters)
    {
        if (is_string($filters)) {
            $filters = json_decode($filters);
        }
        if (is_null($filters)) {
            throw new \Exception("Invalid {$collection->getResource()->getObjectType()} filter");
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

    protected function write($msg)
    {
        if ($this->newLine) {
            echo ($this->logTime ? date('c') . ' ' : '') . str_repeat("\t", $this->tabs) . $msg;
        } else {
            echo $msg;
        }
        $this->newLine = false;
    }

    protected function writeln($msg)
    {
        if ($this->newLine) {
            echo ($this->logTime ? date('c') . ' ' : '') . str_repeat("\t", $this->tabs) . $msg . PHP_EOL;
        } else {
            echo $msg . PHP_EOL;
        }
        $this->newLine = true;
    }
}

$shell = new Shell_EpaceImportFromMongo();
$shell->run();