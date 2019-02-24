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

    public function __construct()
    {
        $this->helper = Mage::helper('epacei');
        $this->helper->setOutput(function($message) {
            $this->log($message);
        });
    }

    public function updateEpaceEntities(Mage_Cron_Model_Schedule $schedule)
    {
        if (!Mage::getStoreConfigFlag(self::XML_PATH_ENABLE)) {
            return;
        }

        $this->log('Start import');
        try {

            if (Mage::getStoreConfigFlag(self::XML_PATH_UPDATE_ESTIMATES)) {
                $this->updateEstimates();
            }
            if (Mage::getStoreConfigFlag(self::XML_PATH_UPDATE_JOBS)) {
                $this->updateJobs();
            }
            if (Mage::getStoreConfigFlag(self::XML_PATH_UPDATE_INVOICES)) {
                $this->updateInvoices();
            }
            if (Mage::getStoreConfigFlag(self::XML_PATH_UPDATE_SHIPMENTS)) {
                $this->updateShipments();
            }

            if (!Mage::getStoreConfigFlag(self::XML_PATH_IMPORT_NEW_OBJECTS)) {
                return;
            }

            $lastUpdateTime = Mage::getStoreConfig('epace/import/last_update_time');
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
                $dateTime = new \DateTime('-14 hours');
            }

            $currentTime = time();

            $this->importNewEstimates($dateTime);
            $this->importNewJobs($dateTime);
            $this->importNewInvoices($dateTime);
            $this->importNewShipments($dateTime);

            Mage::getConfig()->saveConfig(self::XML_PATH_LAST_UPDATE_TIME, $currentTime);
        } finally {
            $this->log('End import');
        }
    }

    protected function importNewEstimates(\DateTime $from)
    {
        /** @var Blackbox_Epace_Model_Resource_Epace_Estimate_Collection $collection */
        $collection = Mage::getResourceModel('efi/estimate_collection');
        $collection->addFilter('entryDate', ['gteq' => $from]);

        $ids = $collection->loadIds();
        foreach ($ids as $estimateId) {
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
        $collection->addFilter('dateSetup', ['gteq' => $from]);

        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_read');
        $orderTable = $resource->getTableName('sales/order');

        $ids = $collection->loadIds();
        foreach ($ids as $jobId) {
            $select = $connection->select()->from($orderTable, 'count(*)')
                ->where('epace_job_id = ?', $jobId);
            if ($connection->fetchOne($select) == 0) {
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
        $collection->addFilter('date', ['gteq' => $from]);

        foreach ($collection->loadIds() as $id) {
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
        $collection->addFilter('invoiceDate', ['gteq' => $from]);

        foreach ($collection->loadIds() as $id) {
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
            return;
        }

        $this->helper->importEstimate($estimate, $magentoEstimate);
        $magentoEstimate->save();

        if ($estimate->isConvertedToJob()) {
            $jobs = $estimate->getJobs();
            if (!empty ($jobs)) {
                foreach ($estimate->getJobs() as $job) {
                    if ($job->getEstimateId() != $estimate->getId()) {
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
            return;
        }

        $this->helper->importJob($job, $order, $magentoEstimate);
        $order->save();

        foreach ($job->getInvoices() as $invoice) {
            try {
                $this->importInvoice($invoice, $order);
            } catch (\Exception $e) {
                $this->log($e->getMessage());
            }
        }

        foreach ($job->getShipments() as $shipment) {
            try {
                $this->importShipment($shipment, $order);
            } catch (\Exception $e) {
                $this->log($e->getMessage());
            }
        }
    }

    protected function importShipment(Blackbox_Epace_Model_Epace_Job_Shipment $jobShipment, Mage_Sales_Model_Order $order = null)
    {
        /** @var Mage_Sales_Model_Order_Shipment $orderShipment */
        $orderShipment = Mage::getModel('sales/order_shipment');

        $orderShipment->load($jobShipment->getId(), 'epace_shipment_id');
        if ($orderShipment->getId()) {
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
            return;
        }

        $this->helper->importInvoice($invoice, $order, $magentoInvoice);
        $magentoInvoice->save();
        if ($invoice->getReceivable()) {
            $this->helper->importReceivable($invoice->getReceivable(), $magentoInvoice)->save();
        }
    }

    protected function updateEstimates()
    {
        /** @var Blackbox_EpaceImport_Model_Resource_Estimate_Collection $collection */
        $collection = Mage::getResourceModel('epacei/estimate_collection');
        $collection->addFieldToFilter('epace_estimate_id', ['notnull' => true]);

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
                    $this->updateEstimate($estimate);
                } catch (\Exception $e) {
                    $this->log($e->getMessage());
                }
            }
        } while ($page < $lastPage);
    }

    protected function updateJobs()
    {
        /** @var Mage_Sales_Model_Resource_Order_Collection $collection */
        $collection = Mage::getResourceModel('sales/order_collection');
        $collection->addFieldToFilter('epace_job_id', ['notnull' => true]);
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
                    $this->updateOrder($order);
                } catch (\Exception $e) {
                    $this->log($e->getMessage());
                }
            }

        } while ($page < $lastPage);
    }

    protected function updateInvoices()
    {
        /** @var Mage_Sales_Model_Entity_Order_Invoice_Collection $collection */
        $collection = Mage::getResourceModel('sales/order_invoice_collection');
        $collection->addFieldToFilter('epace_invoice_id', ['notnull' => true]);

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
                    $this->updateInvoice($invoice);
                } catch (\Exception $e) {
                    $this->log($e->getMessage());
                }
            }

        } while ($page < $lastPage);
    }

    protected function updateShipments()
    {
        /** @var Mage_Sales_Model_Entity_Order_Shipment_Collection $collection */
        $collection = Mage::getResourceModel('sales/order_shipment_collection');
        $collection->addFieldToFilter('epace_shipment_id', ['notnull' => true]);

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
                    $this->updateShipment($shipment);
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
        $this->updateObject($estimate, $newEstimate, [
            'store_name',
            'created_at',
            'updated_at'
        ]);

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
                $this->updateObject($oldItem, $newItem, [
                    'estimate_id',
                    'store_id'
                ]);
                $oldItem->save();
            } else {
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
        $this->updateObject($order, $newOrder, $ignoreFields);

        $ignoreFields = [
            'entity_id',
            'order_id',
            'created_at',
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
                $this->updateObject($oldItem, $newItem, $ignoreFields);
                $oldItem->save();
            } else {
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
                $this->updateObject($oldAddress, $newAddress, $ignoreFields);
                $oldAddress->save();
            } else {
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
        $this->updateObject($invoice, $newInvoice, [
            'order_id',
            'created_at',
            'updated_at'
        ]);

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
        $this->updateObject($receivable, $newReceivable, [
            'created_at',
            'updated_at',
        ]);

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
        $this->updateObject($shipment, $newShipment, [
            'created_at',
            'updated_at'
        ]);

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
    }

    /**
     * @param Mage_Core_Model_Abstract $old
     * @param Mage_Core_Model_Abstract $new
     * @param array $ignoreFields
     */
    protected function updateObject($old, $new, $ignoreFields = [])
    {
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
                $old->setData($key, $value);
            }
        }
    }

    protected function log($message)
    {
        Mage::log($message, null, 'epace_import_cron.log', true);
    }
}