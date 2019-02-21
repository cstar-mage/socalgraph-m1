<?php

class Blackbox_EpaceImport_Helper_Data extends Mage_Core_Helper_Abstract
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

    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $product = null;

    /**
     * @var Mage_Shipping_Model_Carrier_Interface[]
     */
    protected $carriers = null;

    /**
     * @var callable
     */
    protected $output = null;

    /**
     * @param callable $output
     * @return $this
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    public function getShipViaMethodCode(Blackbox_Epace_Model_Epace_Ship_Via $shipVia)
    {
        switch ($shipVia->getShipProvider()->getName()) {
            case 'FedEx':
                $methodCode = $shipVia->getData('fedExMethod');
                break;
            case 'UPS':
                $methodCode = $shipVia->getData('uPSMethod');
                break;
        }
        if (empty($methodCode)) {
            $methodCode = $shipVia->getDescription();
        }

        return strtolower(str_replace(' ', '_', $shipVia->getShipProvider()->getName() . '_' . strtolower($methodCode)));
    }

    public function getJobTypes()
    {
        $data = Mage::getStoreConfig('epace_import/job/types');
        if ($data) {
            $data = json_decode($data, true);
        }

        if (!$data || !$data['types']) {
            $types = Mage::getResourceModel('efi/job_type_collection')->toOptionHash();
            Mage::getConfig()->saveConfig('epace_import/job/types', json_encode(['types' => $types]));
            return $types;
        } else {
            return $data['types'];
        }
    }

    public function importJob(Blackbox_Epace_Model_Epace_Job $job, Mage_Sales_Model_Order $order = null, Blackbox_EpaceImport_Model_Estimate $magentoEstimate = null)
    {
        if (!$order) {
            $order = Mage::getModel('sales/order');
        }

        if ($job->getEstimateId() && (!$magentoEstimate || $magentoEstimate->getEpaceEstimateId() != $job->getEstimateId())) {
            $magentoEstimate = Mage::getModel('epacei/estimate')->load($job->getEstimateId(), 'epace_estimate_id');
        }

        $salesTax = $job->getCustomer()->getSalesTax();
        if ($salesTax && $salesTax->getTaxRate()) {
            $taxPercent = (float)$salesTax->getTaxRate();
        } else {
            $taxPercent = 0;
        }

        $product = $this->getProduct();

        $totalWeight = 0;
//        $estimatedTotalTaxAmount = 0;
//        $partTotalTaxAmount = 0;
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
//            $estimatedTaxAmount = 0;
//            $estimatedTaxPercent = 0;
            if ($quantity) {
                $weight = $quantity->getData('weightPerPiece');
//                $estimatedTaxAmount = $quantity->getTaxAmount();
//                $estimatedTaxPercent = $quantity->getTaxEffectivePercent();
            }

            $totalWeight += $weight;
//            $estimatedTotalTaxAmount += $estimatedTaxAmount;

            if ($salesTax && $salesTax->getTaxRate()) {
                $taxPercent = (float)$salesTax->getTaxRate();
                $taxAmount = $part->getQuotedPrice() * $taxPercent;
            } else {
                $taxPercent = 0;
                $taxAmount = 0;
            }
//            $partTotalTaxAmount += $taxAmount;

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

            if ($part->getProduct()) {
                $name = $part->getProduct()->getDescription() . ' ' . $part->getDescription();
            } else {
                $name = $part->getDescription();
            }

            if ($part->getQuotedPrice() < $part->getEstimatedCost()) {
                $itemDiscount = $part->getEstimatedCost() - $part->getQuotedPrice();
                $itemDiscountPercent = $itemDiscount / $part->getEstimatedCost();
            } else {
                $itemDiscount = 0;
                $itemDiscountPercent = 0;
            }

            $item = Mage::getModel('sales/order_item');
            $item->setData([
                'store_id' => $this->getStore()->getId(),
                'product_id' => $product->getId(),
                'product_type' => $product->getTypeId(),
                'weight' => $weight,
                'sku' => $product->getSku(),
                'name' => $name,
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
                'discount_percent' => $itemDiscountPercent,
                'discount_amount' => $itemDiscount,
                'base_discount_amount' => $itemDiscount,
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
                'base_row_total_incl_tax' => $part->getValue() + $taxAmount,
                'epace_job_part' => $part->getJobPart(),
                'epace_part_original_price' => $part->getOriginalQuotedPrice()
            ]);
            $order->addItem($item);
        }

        $shippingMethod = $this->getShippingMethod($job->getShipVia());

        $customer = $this->getCustomerFromCustomer($job->getCustomer());
        $salesPersonCustomer = $this->getCustomerFromSalesPerson($job->getSalesPerson());

        $subTotal = $job->getJobValue();

        $totalTaxAmount = $job->getAmountToInvoice() * $taxPercent;

        $grandTotal = $job->getAmountToInvoice() + $totalTaxAmount + $job->getFreightAmountTotal();
        if ($grandTotal < $subTotal) {
            $discountAmount = $subTotal - $grandTotal;
        } else {
            $discountAmount = 0;
        }

        // some jobs have empty amountInvoiced
        $amountInvoiced = 0;
        $shippingInvoiced = 0;
        foreach ($job->getInvoices() as $invoice) {
            $amountInvoiced += (float)$invoice->getInvoiceAmount();
            $shippingInvoiced += (float)$invoice->getFreightAmount();
        }

        $invoicedCost = 0;
        foreach ($job->getInvoices() as $invoice) {
            $invoicedCost += (float)$invoice->getTotalCost();
        }

        $shippingAmount = $job->getFreightAmountTotal();
        $shippingInclTax = $shippingAmount;

        $order->addData([
            'estimate_id' => $magentoEstimate ? $magentoEstimate->getId() : null,
            'epace_job_id' => $job->getId(),
            'shipping_description' => $shippingMethod->getCarrierTitle() . ' - ' . $shippingMethod->getMethodTitle(),
            'is_virtual' => 0,
            'store_id' => $this->getStore()->getId(),
            'customer_id' => $customer->getId(),
            'sales_person_id' => $salesPersonCustomer->getId(),
            'base_discount_amount' => $discountAmount,
            'base_grand_total' => $grandTotal,
            'base_shipping_amount' => $shippingAmount,
            'base_shipping_invoiced' => $shippingInvoiced,
            'base_shipping_tax_amount' => 0,
            'base_subtotal' => $subTotal,
            'base_subtotal_invoiced' => min($amountInvoiced, $subTotal),
            'base_tax_amount' => $totalTaxAmount,
            'base_tax_invoiced' => $totalTaxInvoiced,
            'base_to_global_rate' => 1,
            'base_to_order_rate' => 1,
            'base_total_invoiced' => $amountInvoiced,
            'base_total_invoiced_cost' => $invoicedCost,
            'base_total_paid' => $amountInvoiced,
            'discount_amount' => $discountAmount,
            'grand_total' => $grandTotal,
            'shipping_amount' => $shippingAmount,
            'shipping_invoiced' => $shippingInvoiced,
            'shipping_tax_amount' => 0,
            'store_to_base_rate' => 1,
            'store_to_order_rate' => 1,
            'subtotal' => $subTotal,
            'subtotal_invoiced' => min($amountInvoiced, $subTotal),
            'tax_amount' => $totalTaxAmount,
            'tax_invoiced' => $totalTaxInvoiced,
            'total_invoiced' => $amountInvoiced,
            'total_paid' => $amountInvoiced,
            'total_qty_ordered' => $job->getQtyOrdered(),
            'customer_is_guest' => 0,
            'customer_note_notify' => 0,
            'billing_address_id' => null,
            'customer_group_id' => $customer->getGroupId(),
            'email_sent' => 1,
            'base_subtotal_incl_tax' => $subTotal + $taxAmount,
            'subtotal_incl_tax' => $subTotal + $taxAmount,
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
            'shipping_incl_tax' => $shippingInclTax,
            'base_shipping_incl_tax' => $shippingInclTax,
            'job_value' => $job->getJobValue(),
            'job_type' => $job->getTypeId()
        ]);

        $this->setOrderStatus($order, $job->getAdminStatusCode());

        $contacts = [];

        $addedContacts = [];
        $shippingAddressAdded = false;
        $billingAddressAdded = false;
        foreach ($job->getJobContacts() as $jobContact) {
            $contactId = $jobContact->getContactId();
            if (!isset($contacts[$contactId])) {
                $contacts[$contactId] = [
                    'jobContact' => $jobContact,
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

            $this->addAddressToOrder($order, $contact, $type, $contactData['jobContact']);

            $addedContacts[] = $contact->getId();
        }

        if (!$shippingAddressAdded) {
            if (!empty($contacts)) {
                foreach ($contacts as $contactId => $contactData) {
                    /** @var Blackbox_Epace_Model_Epace_Contact $contact */
                    $contact = $contactData['contact'];
                    $this->addAddressToOrder($order, $contact, 'shipping', $contactData['jobContact']);
                    $addedContacts[] = $contact->getId();
                    break;
                }
            } else {
                $order->addAddress(Mage::getModel('sales/order_address')->addData([
                    'customer_id' => $customer->getId(),
                    'address_type' => 'shipping'
                ]));
                $this->writeln('--------------------------EMPTY SHIPPING ADDRESS ADDED-----------------------');
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
                $this->addAddressToOrder($order, $contact, 'billing', $contactData['jobContact']);
                $addedContacts[] = $contact->getId();
            } else {
                $order->addAddress(Mage::getModel('sales/order_address')->addData([
                    'customer_id' => $customer->getId(),
                    'address_type' => 'billing'
                ]));
                $this->writeln('--------------------------EMPTY BILLING ADDRESS ADDED-----------------------');
            }
        }

        /** @var Mage_Sales_Model_Order_Payment $payment */
        $payment = Mage::getModel('sales/order_payment');
        $payment->setMethod('epace_payment');
        $order->setPayment($payment);

        foreach ($job->getNotes() as $note) {
            $order->addStatusHistoryComment($note->getNote());
        }

        return $order;
    }

    public function calculatePartsPriceFromEstimate(Blackbox_Epace_Model_Epace_Job $job)
    {
        if (!$job->isSourceEstimate()) {
            throw new \Exception('Job source is not estimate');
        }
        $amountToInvoice = 0;
        foreach ($job->getParts() as $part) {
            $estimatePart = $part->getEstimatePart();
            if (!$estimatePart) {
                throw new \Exception('Unable to find estimate part for job part ' . $part->getId());
            }

            $quantity = null;
            foreach ($estimatePart->getQuantities() as $_quantity) {
                if ($_quantity->getQuantityOrdered() == $part->getQtyOrdered()) {
                    $quantity = $_quantity;
                    break;
                }
            }

            if (!$quantity) {
                throw new \Exception('Unable to find estimate quantity with qty ordered ' . $part->getQtyOrdered() . ' in estimate part ' . $estimatePart->getId() . ' for job part ' . $part->getId());
            }

            $amountToInvoice += (float)$quantity->getPrice();
        }

        return $amountToInvoice;
    }

    public function calculatePartsPrice(Blackbox_Epace_Model_Epace_Job $job)
    {
        $amountToInvoice = 0;
        foreach ($job->getParts() as $part) {
            $amountToInvoice += (float)$part->getQuotedPrice();
        }
        return $amountToInvoice;
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
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

    protected function addAddressToOrder(Mage_Sales_Model_Order $order, Blackbox_Epace_Model_Epace_Contact $contact, $type, Blackbox_Epace_Model_Epace_Job_Contact $jobContact)
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

        if ($contact->getCustomer()) {
            $customerId = $this->getCustomerFromCustomer($contact->getCustomer())->getId();
        } else {
            $customerId = $order->getCustomerId();
        }

        if ($contact->getSalesPerson()) {
            $salesPersonCustomerId = $this->getCustomerFromSalesPerson($contact->getSalesPerson())->getId();
        } else {
            $salesPersonCustomerId = $order->getSalesPersonId();
        }

        $address = Mage::getModel('sales/order_address');
        $address->addData([
            'customer_id' => $customerId,
            'sales_person_id' => $salesPersonCustomerId,
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
            'company' => $contact->getCompanyName(),
            'epace_job_contact_id' => $jobContact->getId(),
            'epace_contact_id' => $contact->getId()
        ]);
        $order->addAddress($address);

        return $address;
    }

    public function getCustomerFromCustomer(Blackbox_Epace_Model_Epace_Customer $customer)
    {
        if (isset($this->customerCustomerMap[$customer->getId()])) {
            return $this->customerCustomerMap[$customer->getId()];
        }

        $email = $customer->getEmail();
        if (!$email) {
            $email = 'customer' . $customer->getId() . 'epace@socalgraph.com';
        }

        /** @var Mage_Customer_Model_Customer $magentoCustomer */
        $magentoCustomer = Mage::getModel('customer/customer')->setWebsiteId($this->getWebsiteId())->loadByEmail($email);
        if (!$magentoCustomer->getId()) {
            $magentoCustomer
                ->setWebsiteId($this->getWebsiteId())
                ->setStore($this->getStore())
                ->setFirstname($customer->getCustName())
                ->setLastname('')
                ->setEmail($email)
                ->setPassword('password');

            /** @var Mage_Directory_Model_Resource_Region_Collection $regionCollection */
            $regionCollection = Mage::getResourceModel('directory/region_collection');
            $regionCollection->addFieldToFilter('country_id', $customer->getCountry()->getIsoCountry())
                ->addFieldToFilter('code', $customer->getState());
            if ($region = $regionCollection->getFirstItem()) {
                $regionName = $region->getDefaultName();
            } else {
                $regionName = $customer->getState();
            }

            $magentoCustomer->save();

            $address = Mage::getModel('customer/address')->setData([
                'parent_id' => $magentoCustomer->getId(),
                'is_active' => 1,
                'firstname' => $customer->getContactFirstName(),
                'lastname' => $customer->getContactLastName(),
                'company' => $customer->getCustName(),
                'street' => implode(PHP_EOL, array_filter([
                    $customer->getAddress1(),
                    $customer->getAddress2(),
                    $customer->getAddress3()
                ])),
                'city' => $customer->getCity(),
                'country_id' =>  $customer->getCountry()->getIsoCountry(),
                'region' => $regionName,
                'region_id' => $region ? $region->getId() : null,
                'postcode' => $customer->getZip(),
                'telephone' => $customer->getPhoneNumber(),
                'fax' => ''
            ])->save();
            $magentoCustomer->addAddress($address);

            $this->writeln('Created customer ' . $magentoCustomer->getId() . ' from epace Customer ' . $customer->getId());
        }

        return $this->customerCustomerMap[$customer->getId()] = $magentoCustomer;
    }

    public function getCustomerFromSalesPerson(Blackbox_Epace_Model_Epace_SalesPerson $salesPerson)
    {
        if (isset($this->salesPersonCustomerMap[$salesPerson->getId()])) {
            return $this->salesPersonCustomerMap[$salesPerson->getId()];
        }

        $email = $salesPerson->getEmail();
        if (!$email) {
            $email = 'salesPerson' . $salesPerson->getId() .'epace@socalgraph.com';
        }

        $customer = Mage::getModel('customer/customer')->setWebsiteId($this->getWebsiteId())->loadByEmail($email);
        if (!$customer->getId()) {
            $name = explode(' ', $salesPerson->getName(), 2);
            $customer
                ->setWebsiteId($this->getWebsiteId())
                ->setStore($this->getStore())
                ->setFirstname($name[0])
                ->setLastname($name[1])
                ->setEmail($email)
                ->setPassword('password')
                ->setGroupId($this->getWholesaleCustomerGroupId());
            $customer->save();
            $this->writeln('Created customer ' . $customer->getId() . ' from SalesPerson ' . $salesPerson->getId());
        }

        return $this->salesPersonCustomerMap[$salesPerson->getId()] = $customer;
    }

    public function getWholesaleCustomerGroupId()
    {
        if (is_null($this->wholesaleGroupId)) {
            $groupCollection = Mage::getResourceModel('customer/group_collection')
                ->addFieldToFilter('customer_group_code', ['like' => 'wholesale']);
            /** @var Mage_Customer_Model_Group $group */
            $group = $groupCollection->getFirstItem();
            if (!$group->getId()) {
                $group->setCode('Wholesale')->setTaxClassId(3)->save();
            }
            $this->wholesaleGroupId = $group->getId();
        }
        return $this->wholesaleGroupId;
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Ship_Via $shipVia
     * @return Mage_Shipping_Model_Rate_Result_Method
     */
    public function getShippingMethod(Blackbox_Epace_Model_Epace_Ship_Via $shipVia)
    {
        if (is_null($this->carriers)) {
            $this->carriers = Mage::getSingleton('shipping/config')->getActiveCarriers();

            $fedexFound = false;
            $upsFound = false;
            $found = 0;
            foreach ($this->carriers as $carrier) {
                if ($carrier instanceof Mage_Usa_Model_Shipping_Carrier_Fedex) {
                    $fedexFound = true;
                    if (++$found == 2) {
                        break;
                    }
                } else if ($carrier instanceof Mage_Usa_Model_Shipping_Carrier_Ups) {
                    $upsFound = true;
                    if (++$found == 2) {
                        break;
                    }
                }
            }

            if (!$fedexFound) {
                $this->carriers[] = Mage::getModel('usa/shipping_carrier_fedex');
            }
            if (!$upsFound) {
                $this->carriers[] = Mage::getModel('usa/shipping_carrier_ups');
            }
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
            if ($carrier instanceof Mage_Usa_Model_Shipping_Carrier_Fedex) {
                $methods = $carrier->getCode('method');
            } else {
                $methods = $carrier->getAllowedMethods();
            }
            foreach ($methods as $method => $title) {
                if (strtolower($carrier->getCarrierCode() . '_' . $method) == $code) {
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

    public function getWebsiteId()
    {
        return Mage::app()->getWebsite()->getId();
    }

    public function getStore()
    {
        return Mage::app()->getStore();
    }

    protected function writeln($message)
    {
        if (is_callable($this->output)) {
            call_user_func($this->output, $message);
        }
    }
}