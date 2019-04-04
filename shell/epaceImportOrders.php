<?php

include 'abstract.php';

class BlackBox_Shell_EpaceImport extends Mage_Shell_Abstract
{
    /**
     * @var Mage_Customer_Model_Customer[]
     */
    protected $customerCustomerMap = [];

    /**
     * @var Mage_Customer_Model_Customer[]
     */
    protected $salesPersonCustomerMap = [];

    /**
     * @var int
     */
    protected $wholesaleGroupId = null;

    protected $tabs = 0;
    protected $newLine = true;

    /**
     * @var Blackbox_EpaceImport_Helper_Data
     */
    protected $helper;

    public function run()
    {
        if ($this->getArg('ati')) {
            $this->checkAmountToInvoice();
            return;
        }

        if ($this->getArg('dump')) {
            $this->dump();
            return;
        }

        if ($this->getArg('listNotImported')) {
            $this->listNotImported();
            return;
        }

        $this->import();
    }

    protected function _construct()
    {
        $this->helper = Mage::helper('epacei');
        $this->helper->setOutput(function($message) {
            $this->writeln($message);
        });
    }

    protected function checkAmountToInvoice()
    {
        $from = $this->getArg('from');
        $to = $this->getArg('to');
        $job = $this->getArg('job');

        /** @var Blackbox_Epace_Model_Resource_Epace_Job_Collection $collection */
        $collection = Mage::getResourceModel('efi/job_collection');
        if ($from) {
            $collection->addFilter('dateSetup', ['gteq' => new DateTime($from)]);
        }
        if ($to) {
            $collection->addFilter('dateSetup', ['lteq' => new DateTime($to)]);
        }

        if ($job) {
            $collection->addFilter('job', $job);
        }

        $ids = $collection->loadIds();
        foreach ($ids as $id) {
            /** @var Blackbox_Epace_Model_Epace_Job $job */
            $job = Mage::getModel('efi/job')->load($id);
            echo $id . ' ';
            try {
                $amountToInvoice = $this->helper->calculatePartsPriceFromEstimate($job);
                if ($job->getJobValue() == $amountToInvoice) {
                    $this->writeln('Equal. ' . $amountToInvoice . ' ' . $job->getJobValue());
                } else {
                    $this->writeln('Different. ' . $amountToInvoice . ' ' . $job->getJobValue());
                }
            } catch (\Exception $e) {
                $this->writeln($e->getMessage());
            }
        }
    }

    protected function dump()
    {

//        $json = [];
//        /** @var Blackbox_Epace_Model_Resource_Epace_Ship_Provider_Collection $collection */
//        $collection = Mage::getResourceModel('efi/ship_provider_collection');
//        foreach ($collection->getItems() as $provider) {
//            $providerData = $provider->getData();
//            foreach ($provider->getShipVias() as $shipVia) {
//                $providerData['ship_vias'][] = $shipVia->getData();
//            }
//            $json[] = $providerData;
//        }
//        echo json_encode($json, JSON_PRETTY_PRINT);die;

//        $json = [];
//        $collection = Mage::getResourceModel('efi/job_type_collection');
//        foreach ($collection->getItems() as $item) {
//            $json[] = $item->getData();
//        }
//        echo json_encode($json, JSON_PRETTY_PRINT);die;

//        $json = [];
//        /** @var Blackbox_Epace_Model_Resource_Epace_Invoice_Extra_Type_Collection $collection */
//        $collection = Mage::getResourceModel('efi/invoice_extra_type_collection');
//        foreach ($collection->getItems() as $item) {
//            $json[] = $item->getData();
//        }
//        echo json_encode($json, JSON_PRETTY_PRINT);die;

//        /** @var Blackbox_Epace_Model_Resource_Epace_Job_Collection $collection */
//        $collection = Mage::getResourceModel('efi/job_collection');
//        foreach ($collection->loadIds() as $id) {
//            /** @var Blackbox_Epace_Model_Resource_Epace_Job_Part_Collection $partCollection */
//            $partCollection = Mage::getResourceModel('efi/job_part_collection');
//            $partCollection->addFilter('job', $id);
//
//            $invoiceCollection = Mage::getResourceModel('efi/invoice_collection');
//            $invoiceCollection->addFilter('job', $id);
//
//            if ($partCollection->getSize() > 1 && $invoiceCollection->getSize() > 1) {
//                $this->writeln($id);
//            }
//        }die;

//        /** @var Blackbox_Epace_Model_Resource_Epace_Job_Shipment_Collection $collection */
//        $collection = Mage::getResourceModel('efi/job_shipment_collection');
//        foreach ($collection->loadIds() as $id) {
//            /** @var Blackbox_Epace_Model_Epace_Job_Shipment $shipment */
//            $shipment = Mage::getModel('efi/job_shipment')->load($id);
//            $job = $shipment->getJob();
//            if (!$job->isSourceEstimate()) {
//                continue;
//            }
//            /** @var Blackbox_Epace_Model_Resource_Epace_Job_Part_Collection $partCollection */
//            $partCollection = Mage::getResourceModel('efi/job_part_collection');
//            $partCollection->addFilter('job', $job->getId());
//
//            /** @var Blackbox_Epace_Model_Resource_Epace_Job_Product_Collection $jobProductCollection */
//            $jobProductCollection = Mage::getResourceModel('efi/job_product_collection');
//            $jobProductCollection->addFilter('job', $job->getId());
//
//            if ($partCollection->getSize() > 1 && $jobProductCollection->getSize() > 1) {
//                $this->writeln($job->getId());
//            }
//        }
//        return;

//        /** @var Blackbox_Epace_Model_Resource_Epace_Estimate_Collection $collection */
//        $collection = Mage::getResourceModel('efi/estimate_collection');
//        $collection->addFilter('status', Blackbox_Epace_Model_Epace_Estimate_Status::STATUS_CONVERTED_TO_JOB);
//        $ids = $collection->loadIds();
//        $this->writeln(count($ids));
//        /** @var Blackbox_Epace_Model_Resource_Epace_Job_Collection $jobCollection */
//        $jobCollection = Mage::getResourceModel('efi/job_collection');
//        foreach ($ids as $estimateId) {
//            $jobCollection->clearFilters()->clear();
//            $jobIds = $jobCollection->addFilter('altCurrencyRateSourceNote', (int)$estimateId)->addFilter('altCurrencyRateSource', 'Estimate')->loadIds();
//            if (count($jobIds) > 1) {
//                $this->writeln('Estimate ' . $estimateId . '. Job ids: ' . implode(', ', $jobIds));
//            }
//        }

        //var_dump(Mage::getResourceModel('efi/estimate_status_collection')->toOptionHash());die;

        $json = [];
        $customers = [];
        $salesPersons = [];
        $csrs = [];

        /** @var Blackbox_Epace_Model_Resource_Epace_Estimate_Collection $collection */
        $collection = Mage::getResourceModel('efi/estimate_collection');
        //$collection->addFilter('entryDate', ['gt' => new DateTime($this->getArg('date'))]);
        $collection
            ->setOrder('entryDate', Varien_Data_Collection::SORT_ORDER_DESC)
            ->setOrder('entryTime', Varien_Data_Collection::SORT_ORDER_DESC);
        $collection->setPageSize(1)->setCurPage(1);
        //$collection->addFilter('id', 52434);
        //$collection->addFilter('id', 11547);
        //$collection->addFilter('id', 10883);
        if ($estimate = $this->getArg('estimate')) {
            $collection->addFilter('id', $estimate);
        }

        foreach ($collection->getItems() as $estimate) {
            $estimateData = $estimate->getData();

            $customerId = $estimate->getData('customer');
            if (!isset($customers[$customerId])) {
                $customers[$customerId] = $estimate->getCustomer() ? $estimate->getCustomer()->getData() : false;
            }

            $salesPersonId = $estimate->getData('salesPerson');
            if (!isset($salesPersons[$salesPersonId])) {
                $salesPersons[$salesPersonId] = $estimate->getSalesPerson() ? $estimate->getSalesPerson()->getData() : false;
            }

            $csrId = $estimate->getData('csr');
            if (!isset($csrs[$csrId])) {
                $csrs[$csrId] = $estimate->getCSR() ? $estimate->getCSR()->getData() : false;
            }

            foreach ($estimate->getProducts() as $product) {
                $productData = $product->getData();
                foreach ($product->getPriceSummaries() as $priceSummary) {
                    $productData['priceSummaries'][] = $priceSummary->getData();
                }
                foreach ($product->getParts() as $part) {
                    $partData = $part->getData();
                    foreach ($part->getSizeAllowances() as $sizeAllowance) {
                        $partData['sizeAllowances'][] = $sizeAllowance->getData();
                    }
                    foreach ($part->getQuantities() as $quantity) {
                        $partData['quantities'][] = $quantity->getData();
                    }
                    $productData['parts'][] = $partData;
                }
                $estimateData['products'][] = $productData;
            }

            foreach ($estimate->getQuoteLetters() as $quoteLetter) {
                $quoteLetterData = $quoteLetter->getData();
                foreach ($quoteLetter->getNotes() as $note) {
                    $quoteLetterData['_notes'][] = $note->getData();
                }
                $estimateData['quoteLetters'][] = $quoteLetterData;
            }

            if ($estimate->isConvertedToJob()) {
                foreach ($estimate->getJobs() as $job) {
                    $jobData = $job->getData();
                    foreach ($job->getProducts() as $product) {
                        $jobData['products'][] = $product->getData();
                    }
                    foreach ($job->getJobContacts() as $jobContact) {
                        $jobContactData = $jobContact->getData();
                        $jobContactData['_contact'] = $jobContact->getContact()->getData();
                        $customerId = $jobContact->getContact()->getData('customer');
                        if (!isset($customers[$customerId])) {
                            $customer = $jobContact->getContact()->getCustomer();
                            $customers[$customerId] = $customer ? $customer->getData() : false;
                        }
                        $jobData['jobContacts'][] = $jobContactData;
                    }
                    foreach ($job->getInvoices() as $invoice) {
                        $invoiceData = $invoice->getData();

                        if ($invoice->getReceivable()) {
                            $receivableData = $invoice->getReceivable()->getData();
                            foreach ($invoice->getReceivable()->getLines() as $line) {
                                $receivableData['lines'][] = $line->getData();
                            }
                            $invoiceData['_receivable'] = $receivableData;
                        }
                        foreach ($invoice->getCommDists() as $commDist) {
                            $invoiceData['commDists'][] = $commDist->getData();
                        }
                        foreach ($invoice->getExtras() as $extra) {
                            $extraData = $extra->getData();
                            $extraData['type'] = $extra->getType()->getData();
                            $extraData['type']['_salesCategory'] = $extra->getType()->getSalesCategory()->getData();
                            $invoiceData['extras'][] = $extraData;
                        }
                        foreach ($invoice->getLines() as $line) {
                            $invoiceData['lines'][] = $line->getData();
                        }
                        foreach ($invoice->getSalesDists() as $salesDist) {
                            $invoiceData['salesDists'][] = $salesDist->getData();
                        }
                        foreach ($invoice->getTaxDists() as $taxDist) {
                            $invoiceData['taxDists'][] = $taxDist->getData();
                        }

                        $jobData['invoices'][] = $invoiceData;
                    }
                    foreach ($job->getShipments() as $shipment) {
                        $shipmentData = $shipment->getData();
                        $shipmentData['_shipmentType'] = $shipment->getType()->getData();
                        $shipmentData['_shipVia'] = $shipment->getShipVia()->getData();
                        foreach ($shipment->getCartons() as $carton) {
                            $cartonData = $carton->getData();
                            foreach ($carton->getContents() as $content) {
                                $cartonData['contents'][] = $content->getData();
                            }
                            $shipmentData['cartons'][] = $cartonData;
                        }
                        foreach ($shipment->getSkids() as $skid) {
                            $shipmentData['skids'][] = $skid->getData();
                        }
                        $jobData['shipments'][] = $shipmentData;
                    }
                    foreach ($job->getNotes() as $note) {
                        $jobData['notes'][] = $note->getData();
                    }
                    foreach ($job->getParts() as $part) {
                        $partData = $part->getData();
                        foreach ($part->getMaterials() as $material) {
                            $partData['materials'][] = $material->getData();
                        }
                        foreach ($part->getPrePressOps() as $prePressOp) {
                            $partData['prePressOps'][] = $prePressOp->getData();
                        }
                        foreach ($part->getChangeOrders() as $changeOrder) {
                            $changeOrderData = $changeOrder->getData();
                            $changeOrderData['_type'] = $changeOrder->getType()->getData();
                            $partData['changeOrders'][] = $changeOrderData;
                        }
                        foreach ($part->getProofs() as $proof) {
                            $partData['proofs'][] = $proof->getData();
                        }
                        foreach ($part->getItems() as $item) {
                            $partData['items'][] = $item->getData();
                        }
                        foreach ($part->getPressForms() as $pressForm) {
                            $partData['pressForms'][] = $pressForm->getData();
                        }
                        foreach ($part->getComponents() as $component) {
                            $partData['components'][] = $component->getData();
                        }
                        foreach ($part->getFinishingOps() as $finishingOp) {
                            $partData['finishingOps'][] = $finishingOp->getData();
                        }
                        foreach ($part->getOutsidePurchs() as $outsidePurch) {
                            $partData['outsidePurchs'][] = $outsidePurch->getData();
                        }
                        foreach ($part->getPlans() as $plan) {
                            $partData['plans'][] = $plan->getData();
                        }
                        foreach ($part->getCosts() as $cost) {
                            $partData['costs'][] = $cost->getData();
                        }
                        foreach ($part->getSizeAllowances() as $sizeAllowance) {
                            $partData['jobPartSizeAllowances'][] = $sizeAllowance->getData();
                        }
                        $jobData['parts'][] = $partData;
                    }
                    $estimateData['jobs'][] = $jobData;
                }
            }

            $json['estimates'][] = $estimateData;
        }

        $json['customers'] = $customers;
        $json['salesPersons'] = $salesPersons;
        $json['csrs'] = $csrs;

        echo json_encode($json, JSON_PRETTY_PRINT);
    }

    protected function import()
    {
        $from = $this->getArg('from');
        $to = $this->getArg('to');

        if ($this->getArg('mongo')) {
            Blackbox_Epace_Model_Epace_AbstractObject::$useMongo = true;
        }

        if ($this->getArg('salesPersons')) {
            /** @var Blackbox_Epace_Model_Resource_Epace_SalesPerson_Collection $collection */
            $collection = Mage::getResourceModel('efi/salesPerson_collection');
            foreach ($collection->loadIds() as $id) {
                /** @var Blackbox_Epace_Model_Epace_SalesPerson $salesPerson */
                $salesPerson = Mage::getModel('efi/salesPerson')->load($id);
                $customer = $this->helper->getCustomerFromSalesPerson($salesPerson);
                $str = '';
                if ($salesPerson->getEmail() && $customer->getEmail() != $salesPerson->getEmail()) {
                    $str = ' Emails do not match: ' . $salesPerson->getEmail() . ' ' . $customer->getEmail();
                }
                $this->writeln($id . ' ' . $customer->getId() . $str);
            }
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
                            $this->importJob($job, null, false);
                        }
                    }
                }
            } finally {
                $this->tabs--;
            }
        }

        if ($this->getArg('invoices')) {
            /** @var Blackbox_Epace_Model_Resource_Epace_Invoice_Collection $collection */
            $collection = Mage::getResourceModel('efi/invoice_collection');
            if ($from) {
                $collection->addFilter('invoiceDate', ['gteq' => new DateTime($from)]);
            }
            if ($to) {
                $collection->addFilter('invoiceDate', ['lteq' => new DateTime($to)]);
            }

            $ids = $collection->loadIds();
            $count = count($ids);
            $i = 0;
            $this->writeln('Found ' . $count . ' invoices.');
            foreach ($ids as $id) {
                $this->writeln('Invoice ' . ++$i . '/' . $count . ': ' . $id);
                /** @var Blackbox_Epace_Model_Epace_Invoice $invoice */
                $invoice = Mage::getModel('efi/invoice')->load($id);
                try {
                    $this->importInvoice(null, $invoice);
                } catch (\Exception $e) {
                    $this->writeln($e->getMessage());
                }
            }
        }

        if ($this->getArg('shipments')) {
            /** @var Blackbox_Epace_Model_Resource_Epace_Job_Shipment_Collection $collection */
            $collection = Mage::getResourceModel('efi/job_shipment_collection');
            if ($from) {
                $collection->addFilter('date', ['gteq' => new DateTime($from)]);
            }
            if ($to) {
                $collection->addFilter('date', ['lteq' => new DateTime($to)]);
            }

            $ids = $collection->loadIds();
            $count = count($ids);
            $i = 0;
            $this->writeln('Found ' . $count . ' shipments.');
            foreach ($ids as $id) {
                $this->writeln('Shipment ' . ++$i . '/' . $count . ': ' . $id);
                /** @var Blackbox_Epace_Model_Epace_Job_Shipment $shipment */
                $shipment = Mage::getModel('efi/job_shipment')->load($id);
                try {
                    $this->importShipment(null, $shipment);
                } catch (\Exception $e) {
                    $this->writeln($e->getMessage());
                }
            }
        }
    }

    protected function importEstimate(Blackbox_Epace_Model_Epace_Estimate $estimate)
    {
        /** @var Blackbox_EpaceImport_Model_Estimate $magentoEstimate */
        $magentoEstimate = Mage::getModel('epacei/estimate');
        $magentoEstimate->loadByAttribute('epace_estimate_id', $estimate->getId());
        if ($magentoEstimate->getId()) {
            $this->writeln('Estimate ' . $estimate->getId() . ' already imported.');
        } else {
            $this->helper->importEstimate($estimate, $magentoEstimate);
            $magentoEstimate->save();
        }

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
                        $order = $this->importJob($job, $magentoEstimate, true);
                    }
                } finally {
                    $this->tabs--;
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
            $this->writeln('Job ' . $job->getId() . ' already imported');
        } else {
            $this->helper->importJob($job, $order, $magentoEstimate);
            $order->save();
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
                    $invoice = $this->importInvoice($order, $invoice);
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
                    $shipment = $this->importShipment($order, $shipment);
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

    /**
     * @param Mage_Sales_Model_Order $order
     * @param Blackbox_Epace_Model_Epace_Job_Shipment $jobShipment
     * @return Mage_Sales_Model_Order_Shipment
     */
    protected function importShipment($order, Blackbox_Epace_Model_Epace_Job_Shipment $jobShipment)
    {
        /** @var Mage_Sales_Model_Order_Shipment $orderShipment */
        $orderShipment = Mage::getModel('sales/order_shipment');

        $orderShipment->load($jobShipment->getId(), 'epace_shipment_id');
        if ($orderShipment->getId()) {
            $this->writeln("Shipment {$jobShipment->getId()} already imported.");
            return $orderShipment;
        }

        $this->helper->importShipment($jobShipment, $order, $orderShipment);

        $orderShipment->save();

        return $orderShipment;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @param Blackbox_Epace_Model_Epace_Invoice $invoice
     * @return Mage_Sales_Model_Order_Invoice
     */
    protected function importInvoice($order, Blackbox_Epace_Model_Epace_Invoice $invoice)
    {
        /** @var Mage_Sales_Model_Order_Invoice $magentoInvoice */
        $magentoInvoice = Mage::getModel('sales/order_invoice');
        $magentoInvoice->load($invoice->getId(), 'epace_invoice_id');
        if ($magentoInvoice->getId()) {
            $this->writeln("Invoice {$invoice->getId()} already imported.");
            return $magentoInvoice;
        }

        $this->helper->importInvoice($invoice, $order, $magentoInvoice);

        $magentoInvoice->save();

        if ($invoice->getReceivable()) {
            $this->helper->importReceivable($invoice->getReceivable(), $magentoInvoice)->save();
        }

        return $magentoInvoice;
    }

    protected function listNotImported()
    {
        if ($this->getArg('mongo')) {
            Blackbox_Epace_Model_Epace_AbstractObject::$useMongo = true;
        }

        $entities = [
            'Estimate' => [
                'keys' => [
                    'e',
                    'estimates'
                ],
                'dateField' => 'entryDate',
                'magentoClass' => 'epacei/estimate',
                'idField' => 'epace_estimate_id',
            ],
            'Job' => [
                'keys' => [
                    'j',
                    'jobs'
                ],
                'dateField' => 'dateSetup',
                'magentoClass' => 'sales/order',
                'idField' => 'epace_job_id',
            ],
            'Invoice' => [
                'keys' => [
                    'i',
                    'invoices'
                ],
                'dateField' => 'invoiceDate',
                'magentoClass' => 'sales/order_invoice',
                'idField' => 'epace_invoice_id',
            ],
            'JobShipment' => [
                'keys' => [
                    's',
                    'shipments'
                ],
                'dateField' => 'date',
                'magentoClass' => 'sales/order_shipment',
                'idField' => 'epace_shipment_id',
            ]
        ];

        /** @var Blackbox_Epace_Helper_Data $epaceHelper */
        $epaceHelper = Mage::helper('epace');

        foreach ($entities as $entity => $settings) {
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

            $this->write($entity . ' ');

            $epaceModelType = $epaceHelper->getTypeName($entity);

            /** @var Blackbox_Epace_Model_Resource_Epace_Collection $epaceCollection */
            $epaceCollection = Mage::getResourceModel('efi/' . $epaceModelType . '_collection');

            if ($from = $this->getArg('from')) {
                $epaceCollection->addFilter($settings['dateField'], ['gteq' => new \DateTime($from)]);
            }
            if ($to = $this->getArg('to')) {
                $epaceCollection->addFilter($settings['dateField'], ['lteq' => new \DateTime($to)]);
            }
            $ids = $epaceCollection->loadIds();

            $ids = array_filter($ids, function($value) {
                return is_numeric($value) || !empty($value);
            });

            /** @var Mage_Core_Model_Resource_Db_Collection_Abstract $magentoCollection */
            $magentoCollection = Mage::getResourceModel($settings['magentoClass'] . '_collection');
            $connection = $magentoCollection->getConnection();

            $quotedIds = array_map(function($v) use ($connection) {
                return (is_int($v) || preg_match("/^\\d+$/", $v)) ? $v : $connection->quote($v);
            }, $ids);

            $select = $connection->select()->from($magentoCollection->getResource()->getMainTable(), $settings['idField'])
                ->where($settings['idField'] . ' IN (' . implode(',', $quotedIds) . ')');

            $importedIds = $connection->fetchCol($select);

            foreach ($importedIds as $id) {
                $index = array_search($id, $ids);
                if ($index === false) {
                    $this->writeln('Error: id not found. ' . $id);
                } else {
                    unset($ids[$index]);
                }
            }

            $this->writeln(implode(PHP_EOL, $ids));
        }
    }

    protected function getWebsiteId()
    {
        return Mage::app()->getWebsite()->getId();
    }

    protected function getStore()
    {
        return Mage::app()->getStore();
    }

    protected function write($msg)
    {
        if ($this->newLine) {
            echo str_repeat("\t", $this->tabs) . $msg;
        } else {
            echo $msg;
        }
        $this->newLine = false;
    }

    protected function writeln($msg)
    {
        if ($this->newLine) {
            echo str_repeat("\t", $this->tabs) . $msg . PHP_EOL;
        } else {
            echo $msg . PHP_EOL;
        }
        $this->newLine = true;
    }
}

$shell = new BlackBox_Shell_EpaceImport();
$shell->run();