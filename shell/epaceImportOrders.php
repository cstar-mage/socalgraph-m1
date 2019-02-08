<?php

include 'abstract.php';

class BlackBox_Shell_EpaceImport extends Mage_Shell_Abstract
{
    /**
     * @var Mage_Customer_Model_Customer[]
     */
    protected $salesPersonCustomerMap = [];

    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $product = null;

    /**
     * Quote convert object
     *
     * @var Mage_Sales_Model_Convert_Quote
     */
    protected $_convertor;

    public function run()
    {
        //$this->dump();
        $this->import();
    }

    protected function _construct()
    {
        $this->_convertor = Mage::getModel('sales/convert_quote');
    }

    protected function dump()
    {
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
        $collection->addFilter('id', 38329);

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
                        $partData['quanitites'][] = $quantity->getData();
                    }
                    $productData['parts'][] = $partData;
                }
                $estimateData['products'][] = $productData;
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

                        foreach ($invoice->getCommDists() as $commDist) {
                            $invoiceData['commDists'][] = $commDist->getData();
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
                        foreach ($part->getPressForms() as $pressForm) {
                            $partData['pressForms'][] = $pressForm->getData();
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

        /** @var Blackbox_Epace_Model_Resource_Epace_Estimate_Collection $collection */
        $collection = Mage::getResourceModel('efi/estimate_collection');
        if ($from) {
            $collection->addFilter('entryDate', ['gteq' => new DateTime($from)]);
        }
        if ($to) {
            $collection->addFilter('entryDate', ['lteq' => new DateTime($to)]);
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

    protected function importEstimate(Blackbox_Epace_Model_Epace_Estimate $estimate)
    {
        $store = Mage::app()->getStore();
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getModel('sales/quote')->setStoreId($store->getId());

        $customer = $this->loadOrCreateMagentoCustomer($estimate->getSalesPerson());
        $quote->assignCustomer($customer);

        /** @var Blackbox_EpaceImport_Model_Estimate $magentoEstimate */
        $magentoEstimate = Mage::getModel('epacei/estimate');
        $magentoEstimate->loadByAttribute('epace_estimate_id', $estimate->getId());
        if ($magentoEstimate->getId()) {
            $this->writeln('Estimate ' . $estimate->getId() . ' already imported.');
        } else {
            $magentoProduct = $this->getProduct();

            foreach ($estimate->getProducts() as $estimateProduct) {
                foreach ($estimateProduct->getParts() as $part) {
                    foreach ($part->getQuantities() as $quantity) {
                        $item = Mage::getModel('epacei/estimate_item');
                        $item->setData([
                            'product_id' => $magentoProduct->getId(),
                            'store_id' => $store->getId(),
                            'sku' => $magentoProduct->getSku(),
                            'name' => $estimateProduct->getDescription() . ' - ' . $part->getDescription() . ' - ' . $quantity->getQuantityOrdered(),
                            'weight' => (float)$quantity->getWeightPerPiece(),
                            'row_weight' => $quantity->getWeightPerPiece() * $quantity->getQuantityOrdered(),
                            'qty' => $quantity->getData('quantityOrdered'),
                            'price' => (float)$quantity->getData('pricePerEach') * (float)$quantity->getPart()->getData('numSigs'),
                            'base_price' => (float)$quantity->getData('pricePerEach') * (float)$quantity->getPart()->getData('numSigs'),
                            'tax_percent' => $quantity->getTaxEffectivePercent(),
                            'tax_amount' => $quantity->getData('taxAmount'),
                            'base_tax_amount' => $quantity->getData('taxAmount'),
                            'row_total' => $quantity->getData('price'),
                            'base_row_total' => $quantity->getData('price'),
                            'product_type' => $magentoProduct->getTypeId(),
                            'row_total_incl_tax' => $quantity->getData('grandTotal'),
                            'base_row_total_incl_tax' => $quantity->getData('grandTotal'),
                        ]);
                        $magentoEstimate->addItem($item);
                    }
                }
            }

            $aggregateFields = [
                'base_grand_total' => 'base_row_total_incl_tax',
                'grand_total' => 'row_total_incl_tax',
                'base_subtotal' => 'base_row_total',
                'subtotal' => 'row_total',
                'tax_amount' => 'tax_amount',
                'total_qty' => 'qty',
                'base_subtotal_incl_tax' => 'row_total_incl_tax',
                'subtotal_incl_tax' => 'row_total_incl_tax'
            ];
            $aggregatedFields = [];
            foreach ($aggregateFields as $field => $sourceField) {
                $aggregatedFields[$field] = 0;
            }
            foreach ($magentoEstimate->getAllItems() as $item) {
                foreach ($aggregateFields as $field => $sourceField) {
                    $aggregatedFields[$field] += $item->getData($sourceField);
                }
            }

            $magentoEstimate->addData([
                'epace_estimate_id' => $estimate->getId(),
                'status' => $estimate->getData('status'),
                'is_virtual' => 0,
                'store_id' => $this->getStore()->getId(),
                'customer_id' => $customer->getId(),
                'store_to_base_rate' => 1,
                'store_to_order_rate' => 1,
                'increment_id' => 'EPACEESTIMATE_' . $estimate->getId(),
                'base_currency_code' => $this->getStore()->getBaseCurrencyCode(),
                'customer_email' => $customer->getEmail(),
                'customer_firstname' => $customer->getFirstname(),
                'customer_lastname' => $customer->getLastname(),
                'customer_middlename' => $customer->getMiddlename(),
                'customer_prefix' => $customer->getPrefix(),
                'customer_suffix' => $customer->getSuffix(),
                'customer_is_guest' => 0,
                'customer_group_id' => $customer->getGroupId(),
                'global_currency_code' => $this->getStore()->getBaseCurrencyCode(),
                'estimate_currency_code' => $this->getStore()->getBaseCurrencyCode(),
                'store_currency_code' => $this->getStore()->getBaseCurrencyCode(),
                'store_name' => $this->getStore()->getName(),
                'created_at' => strtotime($estimate->getEntryDate()) + strtotime($estimate->getEntryTime()),
                'total_item_count' => count($magentoEstimate->getAllItems()),
            ]);
            $magentoEstimate->addData($aggregatedFields);

            $magentoEstimate->save();
        }

        if ($estimate->isConvertedToJob()) {
            $jobs = $estimate->getJobs();
            if (!empty ($jobs)) {
                $count = count($jobs);
                $this->writeln('Found ' . $count . ' jobs');
                $i = 0;
                foreach ($estimate->getJobs() as $job) {
                    $this->writeln("\t" . 'Job ' . ++$i . '/' . $count);
                    if ($job->getEstimateId() != $estimate->getId()) {
                        $this->writeln('Job source does match with estimate.');
                        continue;
                    }
                    $order = $this->importJob($magentoEstimate, $job);
                }
            }
        }
    }

    protected function addQuoteAddresses(Mage_Sales_Model_Quote $quote, Blackbox_Epace_Model_Epace_Job $job)
    {

    }

    protected function importJob(Blackbox_EpaceImport_Model_Estimate $magentoEstimate, Blackbox_Epace_Model_Epace_Job $job)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order');
        $order->loadByAttribute('epace_job_id', $job->getId());
        if ($order->getId()) {
            $this->writeln("\t" . 'Job ' . $job->getId() . ' already imported');
            return;
        }

        $product = $this->getProduct();

        $totalWeight = 0;
        $totalTaxAmount = 0;
        $totalTaxInvoiced = 0;

        foreach ($job->getParts() as $part) {
            $estimatePart = $part->getEstimatePart();
            /** @var Blackbox_Epace_Model_Epace_Estimate_Quantity $quantity */
            $quantity = null;
            if ($estimatePart) {
                foreach ($estimatePart->getQuantities() as $_quantity) {
                    if ($_quantity->getData('quantityOrdered') == $part->getData('qtyOrdered')) {
                        $quantity = $_quantity;
                        break;
                    }
                }
            }

            $weight = 0;
            $taxPercent = 0;
            $taxAmount = 0;
            if ($quantity) {
                $weight = $quantity->getData('weightPerPiece');
                $taxAmount = $quantity->getTaxAmount();
                $taxPercent = $quantity->getTaxEffectivePercent();
            }

            $totalWeight += $weight;
            $totalTaxAmount += $taxAmount;

            $qtyInvoiced = 0;
            $qtyShipped = 0;
            $rowInvoiced = 0;
            $rowTaxInvoiced = 0;
            foreach ($part->getInvoices() as $invoice) {
                $rowInvoiced += $invoice->getInvoiceAmount();
                $rowTaxInvoiced += $invoice->getTaxAmount();
                foreach ($invoice->getLines() as $line) {
                    $qtyInvoiced += (float)$line->getQtyInvoiced();
                    $qtyShipped += (float)$line->getQtyShipped();
                }
            }
            $totalTaxInvoiced += $rowTaxInvoiced;

            $price = $part->getValue() / $part->getQtyOrdered();
            $estimatedPrice = $part->getEstimatedCost() / $part->getQtyOrdered();

            $priceInclTax = $price + $taxAmount / $part->getQtyOrdered();

            $item = Mage::getModel('sales/order_item');
            $item->setData([
                'store_id' => $this->getStore()->getId(),
                'product_id' => $product->getId(),
                'product_type' => $product->getTypeId(),
                'weight' => $weight,
                'sku' => $product->getSku(),
                'name' => $part->getProduct()->getDescription() . ' ' . $part->getDescription(),
                'qty_canceled' => 0,
                'qty_invoiced' => $qtyInvoiced,
                'qty_ordered' => $part->getQtyOrdered(),
                'qty_refunded' => 0,
                'qty_shipped' => $qtyShipped,
                'price' => $price,
                'base_price' => $price,
                'original_price' => $estimatedPrice,
                'base_original_price' => $estimatedPrice,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'base_tax_amount' => $taxAmount,
                'tax_invoiced' => 0,
                'base_tax_invoiced' => 0,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'base_discount_amount' => 0,
                'discount_invoiced' => 0,
                'base_discount_invoiced' => 0,
                'amount_refunded' => 0,
                'base_amount_refunded' => 0,
                'row_total' => $part->getValue(),
                'base_row_total' => $part->getValue(),
                'row_invoiced' => $rowInvoiced,
                'base_row_invoiced' => $rowInvoiced,
                'row_weight' => $weight * $part->getQtyOrdered(),
                'price_incl_tax' => $priceInclTax,
                'base_price_incl_tax' => $priceInclTax,
                'row_total_incl_tax' => $part->getValue() + $taxAmount,
                'base_row_total_incl_tax' => $part->getValue() + $taxAmount
            ]);
            $order->addItem($item);
        }

        $shippingMethod = $this->getShippingMethod($job->getShipVia());

        $customer = $this->loadOrCreateMagentoCustomer($job->getSalesPerson());

        $order->addData([
            'estimate_id' => $magentoEstimate->getId(),
            'epace_job_id' => $job->getId(),
            'shipping_description' => $shippingMethod->getCarrierTitle() . ' - ' . $shippingMethod->getMethodTitle(),
            'is_virtual' => 0,
            'store_id' => $this->getStore()->getId(),
            'customer_id' => $customer->getId(),
            'base_discount_amount' => 0,
            'base_grand_total' => $job->getJobValue(),
            'base_shipping_amount' => 0,
            'base_shipping_tax_amount' => 0,
            'base_subtotal' => $job->getJobValue(),
            'base_subtotal_invoiced' => $job->getAmountInvoiced(),
            'base_tax_amount' => $totalTaxAmount,
            'base_tax_invoiced' => $totalTaxInvoiced,
            'base_to_global_rate' => 1,
            'base_to_order_rate' => 1,
            'base_total_invoiced' => $job->getAmountInvoiced(),
            'base_total_invoiced_cost' => 0,
            'base_total_paid' => $job->getAmountInvoiced(),
            'grand_total' => $job->getJobValue(),
            'shipping_amount' => 0,
            'shipping_invoiced' => 0,
            'shipping_tax_amount' => 0,
            'store_to_base_rate' => 1,
            'store_to_order_rate' => 1,
            'subtotal' => $job->getJobValue(),
            'subtotal_invoiced' => $job->getAmountInvoiced(),
            'tax_amount' => $totalTaxAmount,
            'tax_invoiced' => $totalTaxInvoiced,
            'total_invoiced' => $job->getAmountInvoiced(),
            'total_paid' => $job->getAmountInvoiced(),
            'total_qty_ordered' => $job->getQtyOrdered(),
            'customer_is_guest' => 0,
            'customer_note_notify' => 0,
            'billing_address_id' => null,
            'customer_group_id' => $customer->getGroupId(),
            'email_sent' => 1,
            'base_subtotal_incl_tax' => $job->getAmountInvoiced(),
            'subtotal_incl_tax' => $job->getAmountInvoiced(),
            'weight' => $totalWeight,
            'increment_id' => 'EPACEJOB_' . $job->getJob(),
            'base_currency_code' => $this->getStore()->getBaseCurrencyCode(),
            'customer_email' => $customer->getEmail(),
            'customer_firstname' => $customer->getFirstname(),
            'customer_lastname' => $customer->getLastname(),
            'customer_middlename' => $customer->getMiddlename(),
            'customer_prefix' => $customer->getPrefix(),
            'customer_suffix' => $customer->getSuffix(),
            'global_currency_code' => $this->getStore()->getBaseCurrencyCode(),
            'order_currency_code' => $this->getStore()->getBaseCurrencyCode(),
            'shipping_method' => $shippingMethod->getCarrier() . '_' . $shippingMethod->getMethod(),
            'store_currency_code' => $this->getStore()->getBaseCurrencyCode(),
            'store_name' => $this->getStore()->getName(),
            'created_at' => strtotime($job->getDateSetup()) + strtotime($job->getTimeSetUp()),
            'total_item_count' => count($job->getParts()),
            'shipping_incl_tax' => 0,
            'base_shipping_incl_tax' => 0
        ]);

        $this->setOrderStatus($order, $job->getAdminStatusCode());

        $contacts = [];
        $contactIdToShipmentMap = [];

        foreach ($job->getShipments() as $shipment) {
            $contactIdToShipmentMap[$shipment->getContactId()][] = $shipment;
        }

        $addedContacts = [];
        $shippingAddressAdded = false;
        $billingAddressAdded = false;
        foreach ($job->getJobContacts() as $jobContact) {
            $contactId = $jobContact->getContactId();
            if (!isset($contacts[$contactId])) {
                $contacts[$contactId] = [
                    'contact' => $jobContact->getContact(),
                    'billing' => $jobContact->getBillTo(),
                    'shipping' => $jobContact->getShipTo()
                ];
            } else {
                $contacts[$contactId]['billing'] |= $jobContact->getBillTo();
                $contacts[$contactId]['shipping'] |= $jobContact->getShipTo();
            }
        }

        foreach ($contacts as $contactId => $contactData) {
            if ($contactData['shipping']) {
                $type = 'shipping';
                $shippingAddressAdded = true;
            } else if ($contactData['billing']) {
                $type = 'billing';
                $billingAddressAdded = true;
            } else {
                continue;
            }
            /** @var Blackbox_Epace_Model_Epace_Contact $contact */
            $contact = $contactData['contact'];

            $this->addAddressToOrder($order, $contact, $type);

            $addedContacts[] = $contact->getId();
        }

        if (!$shippingAddressAdded) {
            foreach ($contacts as $contactId => $contactData) {
                /** @var Blackbox_Epace_Model_Epace_Contact $contact */
                $contact = $contactData['contact'];
                $this->addAddressToOrder($order, $contact, 'shipping');
                $addedContacts[] = $contact->getId();
                break;
            }
        }

        if (!$billingAddressAdded) {
            $shipping = false;
            $contact = null;
            foreach ($contacts as $contactId => $contactData) {
                if (is_null($contact)) {
                    $contact = $contactData['contact'];
                } else if ($contactData['billing']) {
                    $contact = $contactData['contact'];
                    break;
                } else if ($contactData['shipping'] && !$shipping) {
                    $contact = $contactData['contact'];
                    $shipping = true;
                }
            }

            if ($contact) {
                $this->addAddressToOrder($order, $contact, 'billing');
                $addedContacts[] = $contact->getId();
            }
        }

        /** @var Mage_Sales_Model_Order_Payment $payment */
        $payment = Mage::getModel('sales/order_payment');
        $payment->setMethod('epace_payment');
        $order->setPayment($payment);

        $order->save();
    }

    /**
     * @param string $jobStatus
     * @return string
     */
    protected function setOrderStatus(Mage_Sales_Model_Order $order, $jobStatus)
    {
        $statuses = [
            Blackbox_Epace_Model_Epace_Job_Status::STATUS_AUTO_BILLING_OK => 'processing',
            Blackbox_Epace_Model_Epace_Job_Status::STATUS_CLOSED => 'closed',
            Blackbox_Epace_Model_Epace_Job_Status::STATUS_COST_TRANSFER => 'processing',
            Blackbox_Epace_Model_Epace_Job_Status::STATUS_CREDIT_HOLD => 'holded',
            Blackbox_Epace_Model_Epace_Job_Status::STATUS_JOB_CANCELLED => 'canceled',
            Blackbox_Epace_Model_Epace_Job_Status::STATUS_OPEN => ['state' => 'new', 'status' => 'pending'],
            Blackbox_Epace_Model_Epace_Job_Status::STATUS_PARTL_BILL => 'pending',
            Blackbox_Epace_Model_Epace_Job_Status::STATUS_SEND_TO_PRINERGY => 'processing',
            Blackbox_Epace_Model_Epace_Job_Status::STATUS_SHIPPED => 'complete',
            Blackbox_Epace_Model_Epace_Job_Status::STATUS_TO_PLANTMANAGER => 'processing'
        ];
        $status = $statuses[$jobStatus];
        if (!$status) {
            $status = [
                'state' => 'new',
                'status' => 'pending'
            ];
        }
        if (is_array($status)) {
            $order->setData('state', $status['state'])->setData('status', $status['status']);
        } else {
            $order->setData('state', $status)->setData('status', $status);
        }
    }

    protected function addAddressToOrder(Mage_Sales_Model_Order $order, Blackbox_Epace_Model_Epace_Contact $contact, $type)
    {
        /** @var Mage_Directory_Model_Resource_Region_Collection $regionCollection */
        $regionCollection = Mage::getResourceModel('directory/region_collection');
        $regionCollection->addFieldToFilter('country_id', $contact->getCountry()->getIsoCountry())
            ->addFieldToFilter('code', $contact->getState());
        if ($region = $regionCollection->getFirstItem()) {
            $regionName = $region->getDefaultName();
        } else {
            $regionName = $contact->getState();
        }

        if ($contact->getSalesPerson()) {
            $customerId = $this->loadOrCreateMagentoCustomer($contact->getSalesPerson())->getId();
        } else {
            $customerId = $order->getCustomerId();
        }

        $address = Mage::getModel('sales/order_address');
        $address->addData([
            'customer_id' => $customerId,
            'address_type' => $type,
            'fax' => '',
            'region' => $regionName,
            'postcode' => $contact->getZip(),
            'lastname' => $contact->getLastName(),
            'street' => $contact->getAddress1(),
            'city' => $contact->getCity(),
            'email' => $contact->getEmail(),
            'telephone' => $contact->getMobilePhoneNumber() ?: $contact->getBusinessPhoneExtension() . ' ' . $contact->getBusinessPhoneNumber(),
            'country_id' => $contact->getCountry()->getIsoCountry(),
            'firstname' => $contact->getFirstName(),
            'company' => $contact->getCompanyName()
        ]);
        $order->addAddress($address);

        return $address;
    }

    protected function submitOrder(Blackbox_Checkout_Model_Sales_Quote $quote, $incrementId)
    {
        $isVirtual = $quote->isVirtual();

        $transaction = Mage::getModel('core/resource_transaction');
        if ($quote->getCustomerId()) {
            $transaction->addObject($quote->getCustomer());
        }
        $transaction->addObject($quote);

        //$quote->reserveOrderId();
        $quote->setReservedOrderId($incrementId);
        if ($isVirtual) {
            $order = $this->_convertor->addressToOrder($quote->getBillingAddress());
        } else {
            $order = $this->_convertor->addressToOrder($quote->getShippingAddress(true));
        }
        $order->setBillingAddress($this->_convertor->addressToOrderAddress($quote->getBillingAddress()));
        if ($quote->getBillingAddress()->getCustomerAddress()) {
            $order->getBillingAddress()->setCustomerAddress($quote->getBillingAddress()->getCustomerAddress());
        }
        $orderData = [];
        if (!$isVirtual) {
            $fields = array(
                'subtotal_incl_tax',
                'base_subtotal_incl_tax',
                'weight',
                'subtotal',
                'tax_amount',
                'discount_amount',
                'shipping_amount',
                'shipping_incl_tax',
                'shipping_tax_amount',
                'grand_total',
                'base_subtotal',
                'base_tax_amount',
                'base_discount_amount',
                'base_shipping_amount',
                'base_shipping_incl_tax',
                'base_shipping_tax_amount',
                'base_grand_total',
                'hidden_tax_amount',
                'base_hidden_tax_amount',
                'shipping_hidden_tax_amount',
                'base_shipping_hidden_tax_amount',
                'base_shipping_hidden_tax_amnt',
                'shipping_discount_amount',
                'base_shipping_discount_amount',
                'subtotal_incl_tax',
                'base_subtotal_incl_tax'
            );
            foreach ($fields as $field) {
                $orderData[$field] = 0;
            }

            $shippingAddresses = $quote->getAllShippingAddresses();
            $orderAddresses = array();
            foreach($shippingAddresses as $address) {
                foreach ($fields as $field) {
                    $orderData[$field] += (float)$address->getData($field);
                }
                $orderAddresses[] = ($orderAddress = $this->_convertor->addressToOrderAddress($address));
                if ($address->getCustomerAddress()) {
                    $orderAddress->setCustomerAddress($address->getCustomerAddress());
                }
            }
            $order->setShippingAddresses($orderAddresses);
        }
        $order->setPayment($this->_convertor->paymentToOrderPayment($quote->getPayment()));

        foreach ($orderData as $key => $value) {
            $order->setData($key, $value);
        }

        foreach ($quote->getAllItems() as $item) {
            $orderItem = $this->_convertor->itemToOrderItem($item);
            if ($item->getParentItem()) {
                $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
            }
            $order->addItem($orderItem);
        }

        $order->setQuote($quote);

        $order->setStatus('pending')->setState('new');

        $transaction->addObject($order);
        //$transaction->addCommitCallback(array($order, 'place'));
        $transaction->addCommitCallback(array($order, 'save'));

        /**
         * We can use configuration data for declare new order status
         */
        //Mage::dispatchEvent('checkout_type_onepage_save_order', array('order'=>$order, 'quote'=>$quote));
        //Mage::dispatchEvent('sales_model_service_quote_submit_before', array('order'=>$order, 'quote'=>$quote));
        try {
            $transaction->save();
            $quote->setIsActive(false);
            //Mage::dispatchEvent('sales_model_service_quote_submit_success', array('order'=>$order, 'quote'=>$quote));
        } catch (Exception $e) {
            //reset order ID's on exception, because order not saved
            $order->setId(null);
            /** @var $item Mage_Sales_Model_Order_Item */
            foreach ($order->getItemsCollection() as $item) {
                $item->setOrderId(null);
                $item->setItemId(null);
            }

            //Mage::dispatchEvent('sales_model_service_quote_submit_failure', array('order'=>$order, 'quote'=>$quote));
            throw $e;
        }
    }

    protected function importShipment(Mage_Sales_Model_Order $oder, Blackbox_Epace_Model_Epace_Job_Shipment $shipment)
    {

    }

    protected function loadOrCreateMagentoCustomer(Blackbox_Epace_Model_Epace_SalesPerson $salesPerson)
    {
        if (isset($this->salesPersonCustomerMap[$salesPerson->getId()])) {
            return $this->salesPersonCustomerMap[$salesPerson->getId()];
        }

        $customer = Mage::getModel('customer/customer')->setWebsiteId($this->getWebsiteId())->loadByEmail($salesPerson->getEmail());
        if (!$customer->getId()) {
            $name = explode(' ', $salesPerson->getName(), 2);
            $customer
                ->setWebsiteId($this->getWebsiteId())
                ->setStore($this->getStore())
                ->setFirstname($name[0])
                ->setLastname($name[1])
                ->setEmail($salesPerson->getEmail())
                ->setPassword('password');
            if (!$customer->getEmail()) {
                $customer->setEmail('salesPerson' . $salesPerson->getId() .'epace@socalgraph.com');
            }
            $customer->save();
            $this->writeln('Created customer ' . $customer->getId() . ' from SalesPerson ' . $salesPerson->getId());
        }

        return $this->salesPersonCustomerMap[$salesPerson->getId()] = $customer;
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    protected function getProduct()
    {
        if (!$this->product) {
            $this->product = Mage::getModel('catalog/product');
            $id = $this->product->getIdBySku('epace_blank');
            if ($id) {
                $this->product->load($id);
            } else {
                $this->product->setData([
                    'website_ids' => [1],
                    'sku' => 'epace_blank',
                    'name' => 'Epace Blank',
                    'attribute_set_id' => 4,
                    'created_at' => time(),
                    'status' => 0,
                    'tax_class_id' => 4,
                    'visibility' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE
                ])->save();
            }
        }

        return $this->product;
    }

    /**
     * @var Mage_Shipping_Model_Carrier_Interface
     */
    protected $carriers = null;

    /**
     * @param Blackbox_Epace_Model_Epace_Ship_Via $shipVia
     * @return Mage_Shipping_Model_Rate_Result_Method
     */
    protected function getShippingMethod(Blackbox_Epace_Model_Epace_Ship_Via $shipVia)
    {
        if (is_null($this->carriers)) {
            $this->carriers = Mage::getSingleton('shipping/config')->getActiveCarriers();
        }

        /** @var Blackbox_EpaceImport_Helper_Data $helper */
        $helper = Mage::helper('epacei');

        $code = $helper->getShipViaMethodCode($shipVia);
        /** @var Blackbox_EpaceImport_Model_Shipping_Carrier $epaceCarrier */
        $epaceCarrier = null;

        foreach ($this->carriers as $carrier) {
            if ($carrier instanceof Blackbox_EpaceImport_Model_Shipping_Carrier) {
                $epaceCarrier = $carrier;
                continue;
            }
            foreach ($carrier->getAllowedMethods() as $method => $title) {
                if ($carrier->getCarrierCode() . '_' . $method == $code) {
                    return Mage::getModel('shipping/rate_result_method')->setData([
                        'carrier' => $carrier->getCarrierCode(),
                        'method' => $method,
                        'carrier_title' => $carrier->getConfigData('title'),
                        'method_title' => $title
                    ]);
                }
            }
        }

        if ($epaceCarrier) {
            $carrier = $epaceCarrier;
        } else {
            $carrier = Mage::getModel('epacei/shipping_carrier');
        }

        foreach ($carrier->getAllowedMethods() as $method => $title) {
            if ($method == $code) {
                return Mage::getModel('shipping/rate_result_method')->setData([
                    'carrier' => $carrier->getCarrierCode(),
                    'method' => $method,
                    'carrier_title' => $carrier->getConfigData('title'),
                    'method_title' => $title
                ]);
            }
        }

        return $carrier->getEmptyRate();
    }

    protected function getWebsiteId()
    {
        return Mage::app()->getWebsite()->getId();
    }

    protected function getStore()
    {
        return Mage::app()->getStore();
    }

    protected function writeln($message)
    {
        echo $message . PHP_EOL;
    }
}

$shell = new BlackBox_Shell_EpaceImport();
$shell->run();