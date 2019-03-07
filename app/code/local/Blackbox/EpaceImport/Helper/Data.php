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

    protected $salesRepsOptions = null;

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
    
    public function importEstimate(Blackbox_Epace_Model_Epace_Estimate $estimate, Blackbox_EpaceImport_Model_Estimate $magentoEstimate = null)
    {
        if (!$magentoEstimate) {
            $magentoEstimate = Mage::getModel('epacei/estimate');
        }

        $store = $this->getStore();
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
                        'description' => $part->getDescription(),
                        'weight' => (float)$quantity->getWeightPerPiece(),
                        'row_weight' => $quantity->getWeightPerPiece() * $quantity->getQuantityOrdered(),
                        'qty' => $quantity->getData('quantityOrdered'),
                        'base_cost' => $quantity->getCost(),
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
                        'epace_estimate_part_id' => $part->getId(),
                        'epace_estimate_quantity_id' => $quantity->getId(),
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
            'subtotal_incl_tax' => 'row_total_incl_tax',
            'base_total_cost' => 'base_cost'
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

        if ($estimate->getCustomer()) {
            $customer = $this->getCustomerFromCustomer($estimate->getCustomer());
        } else {
            $customer = Mage::getModel('customer/customer');
        }
        $salesPersonCustomer = $this->getCustomerFromSalesPerson($estimate->getSalesPerson());

        $magentoEstimate->addData([
            'epace_estimate_id' => $estimate->getId(),
            'status' => $estimate->getData('status'),
            'is_virtual' => 0,
            'store_id' => $this->getStore()->getId(),
            'customer_id' => $customer->getId(),
            'sales_person_id' => $salesPersonCustomer->getId(),
            'base_to_global_rate' => 1,
            'base_to_estimate_rate' => 1,
            'store_to_base_rate' => 1,
            'store_to_estimate_rate' => 1,
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

        foreach ($estimate->getQuoteLetters() as $quoteLetter) {
            $magentoEstimate->addStatusHistoryComment(implode(PHP_EOL, array_filter([
                $quoteLetter->getSalutation(),
                $quoteLetter->getBody(),
                $quoteLetter->getComment(),
                $quoteLetter->getClosing()
            ])));
        }

        return $magentoEstimate;
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

            $itemDiscount = 0;
            $itemDiscountPercent = 0;
//            if ($part->getQuotedPrice() < $part->getEstimatedCost()) {
//                $itemDiscount = $part->getEstimatedCost() - $part->getQuotedPrice();
//                $itemDiscountPercent = $itemDiscount / $part->getEstimatedCost();
//            } else {
//                $itemDiscount = 0;
//                $itemDiscountPercent = 0;
//            }

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

        $subTotal = $job->getAmountToInvoice();

        $totalTaxAmount = $subTotal * $taxPercent;

        $shippingAmount = $job->getFreightAmountTotal();
        $shippingTaxAmount = $markup = $shippingAmount * 0.4;
        $shippingInclTax = $shippingAmount + $shippingTaxAmount;

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

        $grandTotal = $subTotal + $totalTaxAmount + $shippingInclTax;
        if ($job->getAmountToInvoice() < $job->getJobValue()) {
            $discountAmount = $job->getJobValue() - $job->getAmountToInvoice();
        } else {
            $discountAmount = 0;
        }

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
            'base_shipping_tax_amount' => $shippingTaxAmount,
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
            'shipping_tax_amount' => $shippingTaxAmount,
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
            'job_type' => $job->getTypeId(),
            'base_markup' => $markup,
            'markup' => $markup,
            'amount_to_invoice' => $job->getAmountToInvoice(),
            'change_order_total' => $job->getChangeOrderTotal()
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

    public function importInvoice(Blackbox_Epace_Model_Epace_Invoice $invoice, Mage_Sales_Model_Order $order = null, Mage_Sales_Model_Order_Invoice $magentoInvoice = null)
    {
        if (!$order) {
            $order = Mage::getModel('sales/order')->load($invoice->getData('job'), 'epace_job_id');
            if (!$order) {
                throw new \Exception('Unable to find order for invoice. Invoice id: ' . $invoice->getId() . '. Job: ' . $invoice->getJobId());
            }
        }

        if (!$magentoInvoice) {
            $magentoInvoice = Mage::getModel('sales/order_invoice');
        }


//        $qty = 0;
//        foreach ($invoice->getLines() as $line) {
//            $qty += (float)$line->getQtyInvoiced();
//        }
        $qty = $invoice->getPart()->getQtyOrdered();

        $discount = 0;
        if ($invoice->getReceivable()) {
            $discount = $invoice->getReceivable()->getDiscountApplied();
        } else {
            foreach ($invoice->getExtras() as $extra) {
                if ($extra->getType()->getExtraCategory() == Blackbox_Epace_Model_Epace_Invoice_Extra_Type::EXTRA_CATEGORY_TYPE_DISCOUNT) {
                    $discount += (float)$extra->getPrice();
                }
            }
        }

        $billingAddressId = null;
        $shippingAddressId = null;

        $shipToContact = $invoice->getData('shipToContact');
        $billToContact = $invoice->getData('billToContact');

        if (!empty($shipToContact)) {
            /** @var Mage_Sales_Model_Order_Address $address */
            foreach ($order->getAddressesCollection() as $address) {
                if ($address->isDeleted() || $address->getAddressType() != Mage_Sales_Model_Order_Address::TYPE_SHIPPING) {
                    continue;
                }
                if ($address->getEpaceContactId() == $shipToContact) {
                    $shippingAddressId = $address->getId();
                    break;
                }
            }
        }

        if (!empty($billToContact)) {
            /** @var Mage_Sales_Model_Order_Address $address */
            foreach ($order->getAddressesCollection() as $address) {
                if ($address->isDeleted() || $address->getAddressType() != Mage_Sales_Model_Order_Address::TYPE_BILLING) {
                    continue;
                }
                if ($address->getEpaceContactId() == $billToContact) {
                    $billingAddressId = $address->getId();
                    break;
                }
            }
        }

        $magentoInvoice->setData([
            'store_id' => $this->getStore()->getId(),
            'base_grand_total' => $invoice->getInvoiceAmount(),
            'shipping_tax_amount' => 0,
            'tax_amount' => $invoice->getTaxAmount(),
            'base_tax_amount' => $invoice->getTaxAmount(),
            'store_to_order_rate' => 1,
            'base_shipping_tax_amount' => 0,
            'base_discount_amount' => $discount,
            'base_to_order_rate' => 1,
            'grand_total' => $invoice->getInvoiceAmount(),
            'shipping_amount' => $invoice->getFreightAmount(),
            'subtotal_incl_tax' => (float)$invoice->getLineItemTotal() + (float)$invoice->getTaxAmount(),
            'base_subtotal_incl_tax' => (float)$invoice->getLineItemTotal() + (float)$invoice->getTaxAmount(),
            'store_to_base_rate' => 1,
            'base_shipping_amount' => $invoice->getFreightAmount(),
            'total_qty' => $qty,
            'base_to_global_rate' => 1,
            'subtotal' => $invoice->getLineItemTotal(),
            'base_subtotal' => $invoice->getLineItemTotal(),
            'discount_amount' => $discount,
            'billing_address_id' => $billingAddressId,
            'order_id' => $order->getId(),
            'state' => Mage_Sales_Model_Order_Invoice::STATE_PAID,
            'shipping_address_id' => $shippingAddressId,
            'store_currency_code' => $this->getStore()->getBaseCurrencyCode(),
            'order_currency_code' => $this->getStore()->getBaseCurrencyCode(),
            'base_currency_code' => $this->getStore()->getBaseCurrencyCode(),
            'global_currency_code' => $this->getStore()->getBaseCurrencyCode(),
            'increment_id' => 'EPACEINVOICE_' . $invoice->getId(),
            'created_at' => strtotime($invoice->getDateSetup()) + strtotime($invoice->getTimeSetup()),
            'hidden_tax_amount' => 0,
            'base_hidden_tax_amount' => 0,
            'shipping_hidden_tax_amount' => 0,
            'base_shipping_hidden_tax_amnt' => null,
            'shipping_incl_tax' => $invoice->getFreightAmount(),
            'base_shipping_incl_tax' => $invoice->getFreightAmount(),
            'epace_invoice_id' => $invoice->getId()
        ]);

        $orderItem = null;
        foreach ($order->getAllItems() as $_item) {
            if ($_item->getEpaceJobPart() == $invoice->getJobPart()) {
                $orderItem = $_item;
                break;
            }
        }

        if (!$orderItem) {
            throw new \Exception('Unable to find order item for invoice. Order id: ' . $order->getId() . '. Job: ' . $invoice->getJobId() . '. Invoice epace id: ' . $invoice->getId());
        }

        foreach ($invoice->getLines() as $line) {
            /** @var Mage_Sales_Model_Order_Invoice_Item $invoiceItem */
            $invoiceItem = Mage::getModel('sales/order_invoice_item');
            $invoiceItem->setData([
                'base_price' => 0,
                'tax_amount' => 0,
                'base_row_total' => $line->getTotalPrice(),
                'discount_amount' => null,
                'row_total' => $line->getTotalPrice(),
                'base_discount_amount' => null,
                'price_incl_tax' => 0,
                'base_tax_amount' => 0,
                'base_price_incl_tax' => 0,
                'qty' => $line->getQtyOrdered(),
                'base_cost' => null,
                'price' => 0,
                'base_row_total_incl_tax' => $line->getTotalPrice(),
                'row_total_incl_tax' => $line->getTotalPrice(),
                'product_id' => $orderItem->getProductId(),
                'order_item_id' => $orderItem->getId(),
                'sku' => $orderItem->getSku(),
                'name' => $line->getDescription(),
                'hidden_tax_amount' => 0,
                'base_hidden_tax_amount' => 0,
                'base_weee_tax_applied_amount' => 0,
                'base_weee_tax_applied_row_amnt' => 0,
                'weee_tax_applied_amount' => 0,
                'weee_tax_applied_row_amount' => 0,
                'weee_tax_disposition' => 0,
                'weee_tax_row_disposition' => 0,
                'base_weee_tax_disposition' => 0,
                'base_weee_tax_row_disposition' => 0
            ]);

            $magentoInvoice->addItem($invoiceItem);
        }

        return $magentoInvoice;
    }

    public function importReceivable(Blackbox_Epace_Model_Epace_Receivable $receivable, Mage_Sales_Model_Order_Invoice $magentoInvoice = null)
    {
        $customer = $this->getCustomerFromCustomer($receivable->getCustomer());

        if (!$magentoInvoice) {
            $magentoInvoice = Mage::getModel('sales/order_invoice');
        }

        $magentoReceivable = Mage::getModel('epacei/receivable');
        $magentoReceivable->setData([
            'store_id' => $magentoInvoice->getStoreId(),
            'customer_id' => $customer->getId(),
            'base_grand_total' => $receivable->getInvoiceAmount(),
            'shipping_tax_amount' => 0,
            'tax_amount' => $receivable->getTaxAmount(),
            'base_tax_amount' => $receivable->getTaxAmount(),
            'store_to_order_rate' => 1,
            'base_shipping_tax_amount' => 0,
            'base_discount_amount' => $receivable->getDiscountApplied(),
            'base_to_order_rate' => 1,
            'grand_total' => $receivable->getInvoiceAmount(),
            'shipping_amount' => $receivable->getFreightAmount(),
            'subtotal_incl_tax' => (float)$receivable->getOriginalAmount() + (float)$receivable->getTaxAmount(),
            'base_subtotal_incl_tax' => (float)$receivable->getOriginalAmount() + (float)$receivable->getTaxAmount(),
            'store_to_base_rate' => 1,
            'base_shipping_amount' => $receivable->getFreightAmount(),
            'base_to_global_rate' => 1,
            'subtotal' => $receivable->getOriginalAmount(),
            'base_subtotal' => $receivable->getOriginalAmount(),
            'discount_amount' => $receivable->getDiscountApplied(),
            'order_id' => $magentoInvoice->getOrderId(),
            'invoice_id' => $magentoInvoice->getId(),
            'state' => $receivable->getStatus(),
            'store_currency_code' => $receivable->getAltCurrency(),
            'order_currency_code' => $this->getStore()->getBaseCurrencyCode(),
            'base_currency_code' => $this->getStore()->getBaseCurrencyCode(),
            'global_currency_code' => $this->getStore()->getBaseCurrencyCode(),
            'increment_id' => 'EPACERECEIVABLE_' . $receivable->getId(),
            'customer_firstname' => $receivable->getContactFirstName(),
            'customer_lastname' => $receivable->getContactLastName(),
            'customer_email' => $customer->getEmail(),
            'customer_group_id' => $customer->getGroupId(),
            'created_at' => strtotime($receivable->getDateSetup()) + strtotime($receivable->getTimeSetup()),
            'hidden_tax_amount' => 0,
            'base_hidden_tax_amount' => 0,
            'shipping_hidden_tax_amount' => 0,
            'base_shipping_hidden_tax_amnt' => null,
            'shipping_incl_tax' => $receivable->getFreightAmount(),
            'base_shipping_incl_tax' => $receivable->getFreightAmount(),
            'unpaid_amount' => $receivable->getUnpaidAmount(),
            'invoice_date' => strtotime($receivable->getInvoiceDate()),
            'due_date' => strtotime($receivable->getDueDate()),
            'expected_payment_date' => strtotime($receivable->getExpectedPaymentDate()),
            'date_paid_off' => $receivable->getDatePaidOff() ? strtotime($receivable->getDatePaidOff()) : null,
            'description' => $receivable->getDescription(),
            'gl_register_number' => $receivable->getGlRegisterNumber(),
            'epace_receivable_id' => $receivable->getId()
        ]);

        return $magentoReceivable;
    }

    public function importShipment(Blackbox_Epace_Model_Epace_Job_Shipment $jobShipment, Mage_Sales_Model_Order $order = null, Mage_Sales_Model_Order_Shipment $orderShipment = null)
    {
        if (!$order) {
            $order = Mage::getModel('sales/order')->load($jobShipment->getJobId(), 'epace_job_id');
            if (!$order->getId()) {
                throw new \Exception('Unable to find order for shipment. JobShipment id: ' . $jobShipment->getId() . '. Job: ' . $jobShipment->getJobId() . '.');
            }
        }

        if (!$orderShipment) {
            $orderShipment = Mage::getModel('sales/order_shipment');
        }

        $shippedOrderItems = [];

        $addShipOrderItem = function (Mage_Sales_Model_Order_Item $item, Blackbox_Epace_Model_Epace_Carton_Content $content) use (&$shippedOrderItems) {
            if (!isset($shippedOrderItems[$item->getId()])) {
                $shippedOrderItems[$item->getId()] = [
                    'orderItem' => $item,
                    'qty' => $content->getQuantity()
                ];
            } else {
                $shippedOrderItems[$item->getId()]['qty'] += $content->getQuantity();
            }
        };

        $shippingMethod = null;

        foreach ($jobShipment->getCartons() as $carton) {
            foreach ($carton->getContents() as $content) {
                $partId = false;
                if ($item = $content->getJobPartItem()) {
                    $partId = $item->getJobPart();
                } else if ($material = $content->getJobMaterial()) {
                    $partId = $material->getJobPart();
                } else if ($component = $content->getJobComponent()) {
                    $partId = $component->getJobPart();
                } else if ($pressForm = $content->getJobPartPressForm()) {
                    $partId = $pressForm->getJobPart();
                } else if ($material = $content->getJobMaterial()) {
                    $partId = $material->getJobPart();
                } else if ($part = $content->getJobPart()) {
                    $partId = $part->getJobPart();
                }

                if ($partId) {
                    foreach ($order->getAllItems() as $item) {
                        if ($item->getEpaceJobPart() == $partId) {
                            $addShipOrderItem($item, $content);
                        }
                    }
                } else if ($product = $content->getJobProduct()) {
                    $partIds = [];
                    foreach ($product->getParts() as $part) {
                        $partIds[] = $part->getJobPart();
                    }
                    foreach ($order->getAllItems() as $item) {
                        if (in_array($item->getEpaceJobPart(), $partIds)) {
                            $addShipOrderItem($item, $content);
                        }
                    }
                } else {
                    $contentJob = null;
                    if ($content->getJobPartJob()) {
                        $contentJob = $content->getJobPartJob();
                    } else if ($content->getJob()) {
                        $contentJob = $content->getJob();
                    }
                    if ($contentJob && $contentJob->getId() != $jobShipment->getJob()->getId()) {
                        throw new \Exception("Shipment ({$jobShipment->getId()}) job ({$jobShipment->getJob()->getId()}) do not match with content ({$content->getId()}) job ({$contentJob->getId()}).");
                    }
                    $job = $jobShipment->getJob();
                    foreach ($job->getProducts() as $product) {
                        foreach ($product->getParts() as $part) {
                            $found = false;
                            foreach ($order->getAllItems() as $item) {
                                if ($item->getEpaceJobPart() == $part->getJobPart()) {
                                    $addShipOrderItem($item, $content);
                                    $found = true;
                                    break;
                                }
                            }
                            if (!$found) {
                                throw new \Exception('Not found matching order items for shipment content ' . $content->getId());
                            }
                        }
                    }
                }
            }

            if ($carton->getTrackingNumber()) {
                if (!$shippingMethod) {
                    $shippingMethod = $this->getShippingMethod($jobShipment->getShipVia());
                }

                /** @var Mage_Sales_Model_Order_Shipment_Track $track */
                $track = Mage::getModel('sales/order_shipment_track');
                if ($shippingMethod->getCarrier() == 'epace_shipping') {
                    $title = $shippingMethod->getCarrierTitle() . ': ' . $jobShipment->getShipVia()->getShipProvider()->getName() . ' - ' . $jobShipment->getShipVia()->getDescription();
                } else {
                    $title = $shippingMethod->getCarrierTitle() . ' - ' . $shippingMethod->getMethodTitle();
                }
                $track->setData([
                    'weight' => $carton->getWeight(),
                    'qty' => $carton->getTotalSkidQuantity(),
                    'order_id' => $order->getId(),
                    'track_number' => $carton->getTrackingNumber(),
                    'description' => $carton->getTrackingLink(),
                    'title' => $title,
                    'carrier_code' => $shippingMethod->getCarrier(),
                    'created_at' => strtotime($carton->getActualDate()) + strtotime($carton->getActualTime())
                ]);
                $orderShipment->addTrack($track);
            }
        }

        if (empty($orderShipment->getAllItems())) {
            foreach ($order->getAllItems() as $item) {
                $shippedOrderItems[$item->getId()] = [
                    'orderItem' => $item,
                    'qty' => 0
                ];
            }
        }

        foreach ($shippedOrderItems as $shippedOrderItem) {
            /** @var Mage_Sales_Model_Order_Item $orderItem */
            $orderItem = $shippedOrderItem['orderItem'];
            $shipmentItem = Mage::getModel('sales/order_shipment_item');
            $shipmentItem->setData([
                'price' => $orderItem->getPrice(),
                'weight' => $orderItem->getWeight(),
                'qty' => $shippedOrderItem['qty'],
                'product_id' => $orderItem->getProductId(),
                'order_item_id' => $orderItem->getId(),
                'name' => $orderItem->getName(),
                'sku' => $orderItem->getSku()
            ]);
            $orderShipment->addItem($shipmentItem);
        }

        if ($jobShipment->getContact()->getCustomer()) {
            $customer = $this->getCustomerFromCustomer($jobShipment->getContact()->getCustomer());
            $customerId = $customer->getId();
        } else {
            $customerId = $order->getCustomerId();
        }

        if ($jobShipment->getContact()->getSalesPerson()) {
            $salesPersonCustomer = $this->getCustomerFromSalesPerson($jobShipment->getContact()->getSalesPerson());
            $salesPersonCustomerId = $salesPersonCustomer->getId();
        } else {
            $salesPersonCustomerId = $order->getSalesPersonId();
        }

        $shippingAddressId = null;
        /** @var Mage_Sales_Model_Order_Address $address */
        foreach ($order->getAddressesCollection() as $address) {
            if ($address->isDeleted() || $address->getAddressType() != Mage_Sales_Model_Order_Address::TYPE_SHIPPING) {
                continue;
            }
            if ($address->getEpaceContactId() == $jobShipment->getContactId()) {
                $shippingAddressId = $address->getId();
                break;
            }
        }

        $billingAddressId = null;
        if ($epaceContactId = $jobShipment->getShipBillToContactId()) {
            /** @var Mage_Sales_Model_Order_Address $address */
            foreach ($order->getAddressesCollection() as $address) {
                if ($address->isDeleted() || $address->getAddressType() != Mage_Sales_Model_Order_Address::TYPE_BILLING) {
                    continue;
                }
                if ($address->getEpaceContactId() == $epaceContactId) {
                    $billingAddressId = $address->getId();
                    break;
                }
            }
        } else {
            $billingAddressId = $order->getBillingAddressId();
        }

        $orderShipment->setData([
            'store_id' => $this->getStore()->getId(),
            'total_weight' => $jobShipment->getWeight(),
            'total_qty' => $jobShipment->getQuantity(),
            'email_sent' => null,
            'order_id' => $order->getId(),
            'customer_id' => $customerId,
            'sales_person_id' => $salesPersonCustomerId,
            'shipping_address_id' => $shippingAddressId,
            'billing_address_id' => $billingAddressId,
            //'shipment_status' => '',
            'increment_id' => 'EPACESHIPMENT_' . $jobShipment->getId(),
            'created_at' => strtotime($jobShipment->getDate()) + strtotime($jobShipment->getTime()),
            'packages' => null,
            'shipping_label' => null,
            'epace_shipment_id' => $jobShipment->getId()
        ]);

        return $orderShipment;
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

    public function getSalesRepsOptions()
    {
        if (is_null($this->salesRepsOptions)) {
            $this->salesRepsOptions = [];
            $wholesaleGroup = Mage::getResourceModel('customer/group_collection')->addFieldToFilter('customer_group_code', ['like' => 'wholesale'])->getFirstItem();

            /** @var Mage_Customer_Model_Resource_Customer_Collection $collection */
            $collection = Mage::getResourceModel('customer/customer_collection');
            $collection
                ->addAttributeToSelect('prefix')
                ->addAttributeToSelect('firstname')
                ->addAttributeToSelect('middlename')
                ->addAttributeToSelect('lastname')
                ->addAttributeToSelect('suffix')
                ->addFieldToFilter('group_id', $wholesaleGroup->getId());
            /** @var Mage_Customer_Model_Customer $customer */
            foreach ($collection->getItems() as $customer) {
                $this->salesRepsOptions[$customer->getId()] = $customer->getName();
            }
        }

        return $this->salesRepsOptions;
    }

    protected function writeln($message)
    {
        if (is_callable($this->output)) {
            call_user_func($this->output, $message);
        }
    }
}