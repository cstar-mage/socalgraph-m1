<?php

class Blackbox_EpaceImport_Model_Cron
{
    /** @var  Blackbox_EpaceImport_Helper_Data */
    protected $helper;

    const XML_PATH_ENABLE = 'epace/import/enable';
    const XML_PATH_LAST_UPDATE_TIME = 'epace/import/last_update_time';
    const XML_PATH_UPDATE_ESTIMATES = 'epace/import/update_estimates';
    const XML_PATH_UPDATE_JOBS = 'epace/import/update_jobs';
    const XML_PATH_UPDATE_CLOSED_JOBS = 'epace/import/update_closed_jobs';
    const XML_PATH_UPDATE_INVOICES = 'epace/import/update_invoices';
    const XML_PATH_UPDATE_SHIPMENTS = 'epace/import/update_shipments';
    const XML_PATH_IMPORT_NEW_OBJECTS = 'epace/import/import_new';
    const XML_PATH_LOG = 'epace/import/log';
    const XML_PATH_MONGO_ENABLE = 'epace/mongo/enable';

    /**
     * @var bool
     */
    protected $logEnabled;

    /**
     * @var Mage_Core_Model_Resource
     */
    protected $resource;

    /**
     * @var Varien_Db_Adapter_Interface
     */
    protected $connection;

    public function __construct()
    {
        $this->helper = Mage::helper('epacei');
        $this->helper->setOutput(function($message) {
            $this->log($message);
        });
        $this->logEnabled = Mage::getStoreConfigFlag(self::XML_PATH_LOG);

        $this->resource = Mage::getSingleton('core/resource');
        $this->connection = $this->resource->getConnection('core_read');
    }

    public function updateEpaceEntities(Mage_Cron_Model_Schedule $schedule)
    {
        if (ini_get('max_execution_time') < 1200) {
            ini_set('max_execution_time', 1200);
        }

        if (!Mage::getStoreConfigFlag(self::XML_PATH_ENABLE)) {
            return;
        }

        if (Mage::getStoreConfigFlag(self::XML_PATH_MONGO_ENABLE)) {
            Blackbox_Epace_Model_Epace_AbstractObject::$useMongo = true;
        }

        $this->log('Start import');
        try {
            $lastUpdateTime = Mage::getStoreConfig(self::XML_PATH_LAST_UPDATE_TIME);
            if (!empty($lastUpdateTime)) {
                if (is_numeric($lastUpdateTime)) {
                    $dateTime = new \DateTime();
                    $dateTime->setTimestamp($lastUpdateTime);
                } else {
                    try {
                        $dateTime = new \DateTime($lastUpdateTime);
                    } catch (\Exception $e) {
                        $this->log($e->getMessage());
                    }
                }
            }
            if (!isset($dateTime)) {
                $dateTime = new \DateTime('-1 month');
            }

            if (Mage::getStoreConfigFlag(self::XML_PATH_UPDATE_ESTIMATES)) {
                $this->updateEstimates($dateTime);
            }
            if (Mage::getStoreConfigFlag(self::XML_PATH_UPDATE_JOBS)) {
                $this->updateJobs($dateTime);
            }
            if (Mage::getStoreConfigFlag(self::XML_PATH_UPDATE_INVOICES)) {
                $this->updateInvoices($dateTime);
            }
            if (Mage::getStoreConfigFlag(self::XML_PATH_UPDATE_SHIPMENTS)) {
                $this->updateShipments($dateTime);
            }

            if (!Mage::getStoreConfigFlag(self::XML_PATH_IMPORT_NEW_OBJECTS)) {
                return;
            }

            $currentTime = time();

            $this->importNewEstimates($dateTime);
            $this->importNewJobs($dateTime);
            $this->importNewInvoices($dateTime);
            $this->importNewShipments($dateTime);

            Mage::getConfig()->saveConfig(self::XML_PATH_LAST_UPDATE_TIME, $currentTime);
        } catch (\Exception $e) {
            $this->log('Exception ' . get_class($e) . ': ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
        } finally {
            $this->log('End import');
        }
    }

    protected function importNewEstimates(\DateTime $from)
    {
        /** @var Blackbox_Epace_Model_Resource_Epace_Estimate_Collection $collection */
        $collection = Mage::getResourceModel('efi/estimate_collection');
        if (Blackbox_Epace_Model_Epace_AbstractObject::$useMongo) {
            $collection->addFilter('_created_at', ['gteq' => $from]);
            $collection->addFilter('entryDate', ['gteq' => new \DateTime('2019-01-01')]);
        } else {
            $collection->addFilter('entryDate', ['gteq' => $from]);
        }

        $ids = $collection->loadIds();
        $count = count($ids);
        $i = 0;
        $this->log('Found ' . $count . ' estimates.');
        foreach ($ids as $estimateId) {
            $this->log('Estimate ' . ++$i . '/' . $count . ': ' . $estimateId);
            /** @var Blackbox_Epace_Model_Epace_Estimate $estimate */
            $estimate = Mage::getModel('efi/estimate')->load($estimateId);
            try {
                $this->importEstimate($estimate);
            } catch (\Exception $e) {
                $this->log($e->getMessage());
            }
        }
    }

    protected function importNewJobs(\DateTime $from)
    {
        /** @var Blackbox_Epace_Model_Resource_Epace_Job_Collection $collection */
        $collection = Mage::getResourceModel('efi/job_collection');
        if (Blackbox_Epace_Model_Epace_AbstractObject::$useMongo) {
            $collection->addFilter('_created_at', ['gteq' => $from]);
            $collection->addFilter('dateSetup', ['gteq' => new \DateTime('2019-01-01')]);
        } else {
            $collection->addFilter('dateSetup', ['gteq' => $from]);
        }

        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_read');
        $orderTable = $resource->getTableName('sales/order');

        $ids = $collection->loadIds();
        $count = count($ids);
        $i = 0;
        $this->log('Found ' . $count . ' jobs.');
        foreach ($ids as $jobId) {
            $this->log('Job ' . ++$i . '/' . $count . ': ' . $jobId);
            $select = $connection->select()->from($orderTable, 'count(*)')
                ->where('epace_job_id = ?', $jobId);
            if ($connection->fetchOne($select) > 0) {
                $this->log("Job $jobId already imported.");
            } else  {
                /** @var Blackbox_Epace_Model_Epace_Job $job */
                $job = Mage::getModel('efi/job')->load($jobId);
                try {
                    $this->importJob($job, null, false);
                } catch (\Exception $e) {
                    $this->log($e->getMessage());
                }
            }
        }
    }

    protected function importNewShipments(\DateTime $from)
    {
        /** @var Blackbox_Epace_Model_Resource_Epace_Job_Shipment_Collection $collection */
        $collection = Mage::getResourceModel('efi/job_shipment_collection');
        if (Blackbox_Epace_Model_Epace_AbstractObject::$useMongo) {
            $collection->addFilter('_created_at', ['gteq' => $from]);
            $collection->addFilter('date', ['gteq' => new \DateTime('2019-01-01')]);
        } else {
            $collection->addFilter('date', ['gteq' => $from]);
        }

        $ids = $collection->loadIds();
        $count = count($ids);
        $i = 0;
        $this->log('Found ' . $count . ' shipments.');
        foreach ($ids as $id) {
            $this->log('Shipment ' . ++$i . '/' . $count . ': ' . $id);
            /** @var Blackbox_Epace_Model_Epace_Job_Shipment $shipment */
            $shipment = Mage::getModel('efi/job_shipment')->load($id);
            try {
                $this->importShipment($shipment);
            } catch (\Exception $e) {
                $this->log($e->getMessage());
            }
        }
    }

    protected function importNewInvoices(\DateTime $from)
    {
        /** @var Blackbox_Epace_Model_Resource_Epace_Invoice_Collection $collection */
        $collection = Mage::getResourceModel('efi/invoice_collection');
        if (Blackbox_Epace_Model_Epace_AbstractObject::$useMongo) {
            $collection->addFilter('_created_at', ['gteq' => $from]);
            $collection->addFilter('invoiceDate', ['gteq' => new \DateTime('2019-01-01')]);
        } else {
            $collection->addFilter('invoiceDate', ['gteq' => $from]);
        }

        $ids = $collection->loadIds();
        $count = count($ids);
        $i = 0;
        $this->log('Found ' . $count . ' invoices.');
        foreach ($ids as $id) {
            $this->log('Invoice ' . ++$i . '/' . $count . ': ' . $id);
            /** @var Blackbox_Epace_Model_Epace_Invoice $invoice */
            $invoice = Mage::getModel('efi/invoice')->load($id);
            try {
                $this->importInvoice($invoice);
            } catch (\Exception $e) {
                $this->log($e->getMessage());
            }
        }
    }

    protected function importEstimate(Blackbox_Epace_Model_Epace_Estimate $estimate)
    {
        /** @var Blackbox_EpaceImport_Model_Estimate $magentoEstimate */
        $magentoEstimate = Mage::getModel('epacei/estimate');
        $magentoEstimate->loadByAttribute('epace_estimate_id', $estimate->getId());
        if ($magentoEstimate->getId()) {
            $this->log('Estimate ' . $estimate->getId() . ' already imported.');
            return;
        }

        $this->helper->importEstimate($estimate, $magentoEstimate);
        $string = is_string($magentoEstimate->getCreatedAt()) && !is_numeric($magentoEstimate->getCreatedAt());
        if ($string && strtotime('-10 years') > strtotime($magentoEstimate->getCreatedAt()) || !$string && strtotime('-10 years') > $magentoEstimate->getCreatedAt()) {
            $this->log('[DEBUG] Estimate created_at ' . $magentoEstimate->getCreatedAt() . ' is earlier then 10 years ago');
            $magentoEstimate->unsetData('created_at');
        }
        $magentoEstimate->save();

        $select = $this->connection->select()->from($this->resource->getTableName('epacei/estimate'), 'created_at')
            ->where('entity_id = ?', $magentoEstimate->getId());
        if ($this->connection->fetchOne($select) == '0000-00-00 00:00:00') {
            $this->log('[DEBUG] Estimate created_at saved as 0000-00-00 00:00:00. ' . print_r($estimate->getData(), true) . PHP_EOL . print_r($magentoEstimate->getData()));
        }

        if ($estimate->isConvertedToJob()) {
            $jobs = $estimate->getJobs();
            if (!empty ($jobs)) {
                $count = count($jobs);
                $this->log('Found ' . $count . ' jobs');
                $i = 0;
                foreach ($estimate->getJobs() as $job) {
                    $this->log('Job ' . ++$i . '/' . $count . ': ' . $job->getId());
                    if ($job->getEstimateId() != $estimate->getId()) {
                        $this->log('Job source does match with estimate.');
                        continue;
                    }
                    $this->importJob($job, $magentoEstimate, true);
                }
            }
        }
    }

    protected function importJob(Blackbox_Epace_Model_Epace_Job $job, Blackbox_EpaceImport_Model_Estimate $magentoEstimate = null, $checkImproted = false)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order');

        if ($checkImproted) {
            $order->loadByAttribute('epace_job_id', $job->getId());
        }

        if ($order->getId()) {
            $this->log('Job ' . $job->getId() . ' already imported');
            return;
        }

        $this->helper->importJob($job, $order, $magentoEstimate);
        $string = is_string($order->getCreatedAt()) && !is_numeric($order->getCreatedAt());
        if ($string && strtotime('-10 years') > strtotime($order->getCreatedAt()) || !$string && strtotime('-10 years') > $order->getCreatedAt()) {
            $this->log('[DEBUG] Order created_at ' . $order->getCreatedAt() . ' is earlier then 10 years ago');
            $order->unsetData('created_at');
        }
        $order->save();

        $select = $this->connection->select()->from($this->resource->getTableName('sales/order'), 'created_at')
            ->where('entity_id = ?', $order->getId());
        if ($this->connection->fetchOne($select) == '0000-00-00 00:00:00') {
            $this->log('[DEBUG] Order created_at saved as 0000-00-00 00:00:00. ' . print_r($job->getData(), true) . PHP_EOL . print_r($order->getData()));
        }

        $i = 0;
        $count = count($job->getInvoices());
        foreach ($job->getInvoices() as $invoice) {
            $i++;
            $this->log("Invoice $i/$count: {$invoice->getId()}");
            try {
                $this->importInvoice($invoice, $order);
            } catch (\Exception $e) {
                $this->log('Error: ' . $e->getMessage());
            }
        }

        $i = 0;
        $count = count($job->getShipments());
        foreach ($job->getShipments() as $shipment) {
            $i++;
            $this->log("Shipment $i/$count: {$shipment->getId()}");
            try {
                $this->importShipment($shipment, $order);
            } catch (\Exception $e) {
                $this->log('Error: ' . $e->getMessage());
            }
        }
    }

    protected function importShipment(Blackbox_Epace_Model_Epace_Job_Shipment $jobShipment, Mage_Sales_Model_Order $order = null)
    {
        /** @var Mage_Sales_Model_Order_Shipment $orderShipment */
        $orderShipment = Mage::getModel('sales/order_shipment');

        $orderShipment->load($jobShipment->getId(), 'epace_shipment_id');
        if ($orderShipment->getId()) {
            $this->log("Shipment {$jobShipment->getId()} already imported.");
            return;
        }

        $this->helper->importShipment($jobShipment, $order, $orderShipment);
        $orderShipment->save();
    }

    protected function importInvoice(Blackbox_Epace_Model_Epace_Invoice $invoice, Mage_Sales_Model_Order $order = null)
    {
        /** @var Mage_Sales_Model_Order_Invoice $magentoInvoice */
        $magentoInvoice = Mage::getModel('sales/order_invoice');
        $magentoInvoice->load($invoice->getId(), 'epace_invoice_id');
        if ($magentoInvoice->getId()) {
            $this->log("Invoice {$invoice->getId()} already imported.");
            return;
        }

        $this->helper->importInvoice($invoice, $order, $magentoInvoice);
        $magentoInvoice->save();
        if ($invoice->getReceivable()) {
            $this->helper->importReceivable($invoice->getReceivable(), $magentoInvoice)->save();
        }
    }

    protected function updateEstimates(\DateTime $from)
    {
        /** @var Blackbox_EpaceImport_Model_Resource_Estimate_Collection $collection */
        $collection = Mage::getResourceModel('epacei/estimate_collection');

        if (Blackbox_Epace_Model_Epace_AbstractObject::$useMongo) {
            /** @var Blackbox_Epace_Model_Resource_Epace_Estimate_Collection $mongoCollection */
            $mongoCollection = Mage::getResourceModel('efi/estimate_collection');
            $mongoCollection->addFilter('_updated_at', ['gt' => $from]);
            $ids = $mongoCollection->loadIds();
            $collection->addFieldToFilter('epace_estimate_id', ['in' => $ids]);
        } else {
            $collection->addFieldToFilter('epace_estimate_id', ['notnull' => true]);
        }

        $page = 0;
        $collection->setPageSize(100);
        $lastPage = $collection->getLastPageNumber();

        do {
            $page++;

            $collection->clear()->setCurPage($page)->load();
            /** @var Blackbox_EpaceImport_Model_Estimate $estimate */
            foreach ($collection->getItems() as $estimate) {
                $estimate->setDataChanges(false);
                try {
                    $this->log('Updating estimate ' . $estimate->getId() . '. Epace estimate id: ' . $estimate->getEpaceEstimateId());
                    $this->updateEstimate($estimate);
                } catch (\Exception $e) {
                    $this->log($e->getMessage());
                }
            }
        } while ($page < $lastPage);
    }

    protected function updateJobs(\DateTime $from)
    {
        /** @var Mage_Sales_Model_Resource_Order_Collection $collection */
        $collection = Mage::getResourceModel('sales/order_collection');
        if (Blackbox_Epace_Model_Epace_AbstractObject::$useMongo) {
            /** @var Blackbox_Epace_Model_Resource_Epace_Job_Collection $mongoCollection */
            $mongoCollection = Mage::getResourceModel('efi/job_collection');
            $mongoCollection->addFilter('_updated_at', ['gt' => $from]);
            $ids = $mongoCollection->loadIds();
            $collection->addFieldToFilter('epace_job_id', ['in' => $ids]);
        } else {
            $collection->addFieldToFilter('epace_job_id', ['notnull' => true]);
        }
        if (!Mage::getStoreConfigFlag(self::XML_PATH_UPDATE_CLOSED_JOBS)) {
            $collection->addFieldToFilter('state', ['neq' => Mage_Sales_Model_Order::STATE_CLOSED]);
        }

        $page = 0;
        $collection->setPageSize(100);
        $lastPage = $collection->getLastPageNumber();

        do {
            $page++;

            $collection->clear()->setCurPage($page)->load();
            /** @var Mage_Sales_Model_Order $order */
            foreach ($collection->getItems() as $order) {
                $order->setDataChanges(false);
                try {
                    $this->log('Updating order ' . $order->getId() . '. Job: ' . $order->getEpaceJobId());
                    $this->updateOrder($order);
                } catch (\Exception $e) {
                    $this->log($e->getMessage());
                }
            }

        } while ($page < $lastPage);
    }

    protected function updateInvoices(\DateTime $from)
    {
        /** @var Mage_Sales_Model_Entity_Order_Invoice_Collection $collection */
        $collection = Mage::getResourceModel('sales/order_invoice_collection');
        if (Blackbox_Epace_Model_Epace_AbstractObject::$useMongo) {
            /** @var Blackbox_Epace_Model_Resource_Epace_Invoice_Collection $mongoCollection */
            $mongoCollection = Mage::getResourceModel('efi/invoice_collection');
            $mongoCollection->addFilter('_updated_at', ['gt' => $from]);
            $ids = $mongoCollection->loadIds();
            $collection->addFieldToFilter('epace_invoice_id', ['in' => $ids]);
        } else {
            $collection->addFieldToFilter('epace_invoice_id', ['notnull' => true]);
        }

        $page = 0;
        $collection->setPageSize(100);
        $lastPage = $collection->getLastPageNumber();

        do {
            $page++;

            $collection->clear()->setCurPage($page)->load();
            /** @var Mage_Sales_Model_Order_Invoice $invoice */
            foreach ($collection->getItems() as $invoice) {
                $invoice->setDataChanges(false);
                try {
                    $this->log('Updating invoice ' . $invoice->getId() . '. Epace invoice id: ' . $invoice->getEpaceInvoiceId());
                    $this->updateInvoice($invoice);
                } catch (\Exception $e) {
                    $this->log($e->getMessage());
                }
            }

        } while ($page < $lastPage);
    }

    protected function updateShipments(\DateTime $from)
    {
        /** @var Mage_Sales_Model_Entity_Order_Shipment_Collection $collection */
        $collection = Mage::getResourceModel('sales/order_shipment_collection');
        if (Blackbox_Epace_Model_Epace_AbstractObject::$useMongo) {
            /** @var Blackbox_Epace_Model_Resource_Epace_Job_Shipment_Collection $mongoCollection */
            $mongoCollection = Mage::getResourceModel('efi/job_shipment_collection');
            $mongoCollection->addFilter('_updated_at', ['gt' => $from]);
            $ids = $mongoCollection->loadIds();
            $collection->addFieldToFilter('epace_shipment_id', ['in' => $ids]);
        } else {
            $collection->addFieldToFilter('epace_shipment_id', ['notnull' => true]);
        }

        $page = 0;
        $collection->setPageSize(100);
        $lastPage = $collection->getLastPageNumber();

        do {
            $page++;

            $collection->clear()->setCurPage($page)->load();
            /** @var Mage_Sales_Model_Order_Shipment $shipment */
            foreach ($collection->getItems() as $shipment) {
                $shipment->setDataChanges(false);
                try {
                    $this->log('Updating shipment ' . $shipment->getId() . '. Epace shipment id: ' . $shipment->getEpaceShipmentId());
                    $this->updateShipment($shipment);
                    $shipment->getOrder()->reset();
                    $shipment->dispose();
                    gc_collect_cycles();
                } catch (\Exception $e) {
                    $this->log($e->getMessage());
                }
            }

        } while ($page < $lastPage);
    }

    protected function updateEstimate(Blackbox_EpaceImport_Model_Estimate $estimate)
    {
        /** @var Blackbox_Epace_Model_Epace_Estimate $epaceEstimate */
        $epaceEstimate = Mage::getModel('efi/estimate')->load($estimate->getEpaceEstimateId());
        if (!$epaceEstimate->getId()) {
            return;
        }

        $newEstimate = $this->helper->importEstimate($epaceEstimate);
        $changes = $this->updateObject($estimate, $newEstimate, [
            'store_name',
//            'created_at',
            'updated_at'
        ]);
        $this->logChanges('Estimate ' . $estimate->getId() . ' updates', $changes);

        $oldItems = $estimate->getAllItems();
        foreach ($oldItems as $item) {
            $item->setDataChanges(false);
        }

        foreach ($newEstimate->getAllItems() as $newItem) {
            $oldItem = null;
            foreach ($oldItems as $_oldItem) {
                if ($_oldItem->getEpaceEstimateQuantityId() == $newItem->getEpaceEstimateQuantityId()) {
                    $oldItem = $_oldItem;
                    break;
                }
            }

            if ($oldItem) {
                $changes = $this->updateObject($oldItem, $newItem, [
                    'estimate_id',
                    'store_id'
                ]);
                $this->logChanges('Estimate item ' . $oldItem->getId() . ' updates', $changes);
                $oldItem->save();
            } else {
                $this->log('Added estimate item ' . print_r($newItem->getData(), true));
                $estimate->addItem($newItem);
                $newItem->save();
            }
        }

        $oldStatusHistories = $estimate->getAllStatusHistory();
        foreach ($newEstimate->getAllStatusHistory() as $statusHistory) {
            foreach ($oldStatusHistories as $oldStatusHistory) {
                if ($statusHistory->getComment() == $oldStatusHistory->getComment()) {
                    continue 2;
                }
            }

            $estimate->addStatusHistory($statusHistory);
            $statusHistory->save();
        }

        $estimate->save();
    }

    protected function updateOrder(Mage_Sales_Model_Order $order)
    {
        $job = Mage::getModel('efi/job')->load($order->getEpaceJobId());
        if (!$job->getId()) {
            return;
        }

        $ignoreFields = [
            'entity_id',
            'billing_address_id',
            'shipping_address_id',
            'created_at',
            'updated_at',
            'store_name',
        ];

        $newOrder = $this->helper->importJob($job);
        $changes = $this->updateObject($order, $newOrder, $ignoreFields);
        $this->logChanges('Order ' . $order->getId() . ' updates', $changes);

        $ignoreFields = [
            'entity_id',
            'order_id',
//            'created_at',
            'updated_at'
        ];

        $oldItems = $order->getAllItems();
        foreach ($oldItems as $item) {
            $item->setDataChanges(false);
        }

        foreach ($newOrder->getAllItems() as $newItem) {
            $oldItem = null;
            foreach ($oldItems as $_oldItem) {
                if ($_oldItem->getEpaceJobPart() == $newItem->getEpaceJobPart()) {
                    $oldItem = $_oldItem;
                    break;
                }
            }

            if ($oldItem) {
                $changes = $this->updateObject($oldItem, $newItem, $ignoreFields);
                $this->logChanges('Order item ' . $oldItem->getId() . ' updates', $changes);
                $oldItem->save();
            } else {
                $this->log('Added new order item ' . print_r($newItem->getData(), true));
                $order->addItem($newItem);
                $newItem->save();
            }
        }

        $ignoreFields = [
            'entity_id',
            'parent_id'
        ];

        $oldAddresses = $order->getAddressesCollection()->getItems();
        foreach ($oldAddresses as $address) {
            $address->setDataChanges(false);
        }

        foreach ($newOrder->getAddressesCollection() as $newAddress) {
            $oldAddress = null;
            foreach ($oldAddresses as $_oldAddress) {
                if ($_oldAddress->getEpaceJobContactId() == $newAddress->getEpaceJobContactId() && $_oldAddress->getAddressType() == $newAddress->getAddressType()) {
                    $oldAddress = $_oldAddress;
                    break;
                }
            }

            if ($oldAddress) {
                $changes = $this->updateObject($oldAddress, $newAddress, $ignoreFields);
                $this->logChanges('Order address ' . $oldAddress->getId() . ' updates', $changes);
                $oldAddress->save();
            } else {
                $this->log('Added new order address ' . print_r($newAddress->getData(), true));
                $order->addAddress($newAddress);
                $newAddress->save();
            }
        }

        $oldStatusHistories = $order->getStatusHistoryCollection()->getItems();

        foreach ($newOrder->getStatusHistoryCollection()->getItems() as $newStatusHistory) {
            foreach ($oldStatusHistories as $oldStatusHistory) {
                if ($oldStatusHistory->getComment() == $newStatusHistory->getComment()) {
                    continue 2;
                }
            }
            $order->addStatusHistoryComment($newStatusHistory->getComment());
        }

        $order->save();
    }

    protected function updateInvoice(Mage_Sales_Model_Order_Invoice $invoice)
    {
        /** @var Blackbox_Epace_Model_Epace_Invoice $epaceInvoice */
        $epaceInvoice = Mage::getModel('efi/invoice')->load($invoice->getEpaceInvoiceId());
        if (!$epaceInvoice->getId()) {
            return;
        }

        $newInvoice = $this->helper->importInvoice($epaceInvoice);
        $changes = $this->updateObject($invoice, $newInvoice, [
            'order_id',
//            'created_at',
            'updated_at'
        ]);
        $this->logChanges('Invoice ' . $invoice->getId() . ' updates', $changes);

        if ($epaceInvoice->getReceivable()) {
            $magentoReceivable = Mage::getModel('epacei/receivable')->load($invoice->getId(), 'invoice_id');
            if ($magentoReceivable->getId()) {
                $this->updateReceivable($magentoReceivable, $epaceInvoice->getReceivable(), $invoice);
            } else {
                $this->helper->importReceivable($epaceInvoice->getReceivable(), $invoice)->save();
            }
        }

        $invoice->save();
    }

    protected function updateReceivable(Blackbox_EpaceImport_Model_Receivable $receivable, Blackbox_Epace_Model_Epace_Receivable $epaceReceivable, Mage_Sales_Model_Order_Invoice $invoice)
    {
        $newReceivable = $this->helper->importReceivable($epaceReceivable, $invoice);
        $changes = $this->updateObject($receivable, $newReceivable, [
//            'created_at',
            'updated_at',
        ]);
        $this->logChanges('Receivable ' . $receivable->getId() . ' updates', $changes);

        $receivable->save();
    }

    protected function updateShipment(Mage_Sales_Model_Order_Shipment $shipment)
    {
        /** @var Blackbox_Epace_Model_Epace_Job_Shipment $epaceShipment */
        $epaceShipment = Mage::getModel('efi/job_shipment')->load($shipment->getEpaceShipmentId());
        if (!$epaceShipment->getId()) {
            return;
        }

        $newShipment = $this->helper->importShipment($epaceShipment, $shipment->getOrder());
        $epaceShipment->dispose();
        $changes = $this->updateObject($shipment, $newShipment, [
//            'created_at',
            'updated_at'
        ]);
        $this->logChanges('Shipment ' . $shipment->getId() . ' updates', $changes);

        /** @var Mage_Sales_Model_Order_Shipment_Track[] $oldTracks */
        $oldTracks = $shipment->getAllTracks();
        foreach ($newShipment->getAllTracks() as $track) {
            foreach ($oldTracks as $oldTrack) {
                if ($oldTrack->getNumber() == $track->getNumber()) {
                    continue 2;
                }
            }

            $shipment->addTrack($track);
        }

        $shipment->save();
        $newShipment->getOrder()->reset();
        $newShipment->dispose();
    }

    /**
     * @param Mage_Core_Model_Abstract $old
     * @param Mage_Core_Model_Abstract $new
     * @param array $ignoreFields
     * @return array
     */
    protected function updateObject($old, $new, $ignoreFields = [])
    {
        $changes = [];
        $fields = $old->getResource()->getReadConnection()->describeTable($old->getResource()->getMainTable());
        foreach ($new->getData() as $key => $value) {
            if (in_array($key, $ignoreFields)) {
                continue;
            }
            $oldValue = $old->getData($key);
            if (!is_null($value)) {
                $fieldDescription = $fields[$key];
                if ($fieldDescription) {
                    switch ($fieldDescription['DATA_TYPE']) {
                        case 'decimal':
                            $value = round($value, $fieldDescription['SCALE']);
                            break;
                        case 'timestamp':
                            if (is_string($oldValue) && !is_numeric($oldValue)) {
                                $oldValue = strtotime($oldValue);
                            }
                            if (is_string($value) && !is_numeric($value)) {
                                $value = strtotime($value);
                            }
                            break;
                    }
                }
            }
            if ($oldValue!= $value) {
                $changes[] = [
                    'field' => $key,
                    'from' => $old->getData($key),
                    'to' => $new->getData($key)
                ];
                $old->setData($key, $value);
            }
        }
        return $changes;
    }

    protected function logChanges($message, $changes)
    {
        if (!$this->logEnabled || empty($changes)) {
            return;
        }
        $msg = [];
        foreach ($changes as $change) {
            $msg[] = $change['field'] . ': ' . $change['from'] . ' => ' . $change['to'] . '.';
        }
        $this->log($message . '. ' . implode(' ', $msg));
    }

    protected function log($message)
    {
        if ($this->logEnabled) {
            Mage::log($message, null, 'epace_import_cron.log', true);
        }
    }
}