<?php

class Blackbox_SummarInvoice_Model_Summar_Invoice extends Varien_Object
{
    const NOTIFICATION_TYPE_MULTIINVOICE = 501147;

    protected $invoiceCollection = null;
    /** @var Mage_Sales_Model_Order[] */
    protected $orders = null;
    /** @var Mage_Sales_Model_Order_Address[] */
    protected $billingAddresses = null;
    /** @var Mage_Sales_Model_Order_Address[] */
    protected $shippingAddresses = null;
    /** @var Mage_Sales_Model_Order_Invoice_Item[] */
    protected $items = null;

    public function collectData($customer, $from, $to)
    {
        $this->orders = [];
        $this->billingAddresses = [];
        $this->shippingAddresses = [];
        $this->_data = [];

        /** @var Mage_Sales_Model_Resource_Order_Invoice_Collection $collection */
        $collection = Mage::getResourceModel('sales/order_invoice_collection');
        $collection->join(['o' => 'sales/order'], 'o.entity_id = main_table.order_id', 'customer_id');
        if ($customer) {
            $collection->addAttributeToFilter('customer_id', $customer->getId());
        }
        $collection->addAttributeToFilter('main_table.created_at', array('from' => $from, 'to' => $to));

        $fields = [
            'base_discount_amount',
            'base_discount_canceled',
            'base_discount_invoiced',
            'base_discount_refunded',
            'base_grand_total',
            'base_shipping_amount',
            'base_shipping_canceled',
            'base_shipping_invoiced',
            'base_shipping_refunded',
            'base_shipping_tax_amount',
            'base_shipping_tax_refunded',
            'base_subtotal',
            'base_subtotal_canceled',
            'base_subtotal_invoiced',
            'base_subtotal_refunded',
            'base_tax_amount',
            'base_tax_canceled',
            'base_tax_invoiced',
            'base_tax_refunded',
            'base_to_global_rate',
            'base_to_order_rate',
            'base_total_canceled',
            'base_total_invoiced',
            'base_total_invoiced_cost',
            'base_total_offline_refunded',
            'base_total_online_refunded',
            'base_total_paid',
            'base_total_qty_ordered',
            'base_total_refunded',
            'discount_amount',
            'discount_canceled',
            'discount_invoiced',
            'discount_refunded',
            'grand_total',
            'shipping_amount',
            'shipping_canceled',
            'shipping_invoiced',
            'shipping_refunded',
            'shipping_tax_amount',
            'shipping_tax_refunded',
            'store_to_base_rate',
            'store_to_order_rate',
            'subtotal',
            'subtotal_canceled',
            'subtotal_invoiced',
            'subtotal_refunded',
            'tax_amount',
            'tax_canceled',
            'tax_invoiced',
            'tax_refunded',
            'total_canceled',
            'total_invoiced',
            'total_offline_refunded',
            'total_online_refunded',
            'total_paid',
            'total_qty_ordered',
            'total_refunded',

            'adjustment_negative',
            'adjustment_positive',
            'base_adjustment_negative',
            'base_adjustment_positive',
            'base_shipping_discount_amount',
            'base_subtotal_incl_tax',
            'base_total_due',
            'payment_authorization_amount',
            'shipping_discount_amount',
            'subtotal_incl_tax',
            'total_due',
            'weight',

            'hidden_tax_amount',
            'base_hidden_tax_amount',
            'shipping_hidden_tax_amount',
            'base_shipping_hidden_tax_amnt',
            'hidden_tax_invoiced',
            'base_hidden_tax_invoiced',
            'hidden_tax_refunded',
            'base_hidden_tax_refunded',
            'shipping_incl_tax',
            'base_shipping_incl_tax'
        ];

        $itemFields = [
            'base_price',
            'tax_amount',
            'base_row_total',
            'discount_amount',
            'row_total',
            'base_discount_amount',
            'price_incl_tax',
            'base_tax_amount',
            'base_price_incl_tax',
            'qty',
            'base_cost',
            'price',
            'base_row_total_incl_tax',
            'row_total_incl_tax',

            'hidden_tax_amount',
            'base_hidden_tax_amount',
            'base_weee_tax_applied_amount',
            'base_weee_tax_applied_row_amnt',
            'weee_tax_applied_amount',
            'weee_tax_applied_row_amount',

            'weee_tax_disposition',
            'weee_tax_row_disposition',
            'base_weee_tax_disposition',
            'base_weee_tax_row_disposition'
        ];

        /** @var Mage_Sales_Model_Order_Invoice $invoice */
        foreach ($collection as $invoice) {
            foreach ($fields as $field) {
                $this->_data[$field] += (float)$invoice->getData($field);
            }

            foreach ($invoice->getAllItems() as $item)
            {
                $summarItem = $this->_findItem($item);
                if (!$summarItem) {
                    $this->items[] = $item;
                    continue;
                }

                $data = $summarItem->getData();

                foreach ($itemFields as $field) {
                    $data[$field] += (float)$item->getData($field);
                }

                $summarItem->setData($data);
            }

            $this->orders[] = $invoice->getOrder();
            $this->billingAddresses[] = $invoice->getBillingAddress();
            $this->shippingAddresses[] = $invoice->getShippingAddress();
        }
    }

    public function getOrder()
    {
        return $this->orders[0];
    }

    public function getOrders()
    {
        return $this->orders;
    }

    public function getBillingAddresses()
    {
        return $this->billingAddresses;
    }

    public function getShippingAddresses()
    {
        return $this->shippingAddresses;
    }

    public function getAllItems()
    {
        return $this->items;
    }

    public function sendEmail()
    {
        if (!$this->getOrder()) {
            return false;
        }

        // Start store emulation process
        /** @var $appEmulation Mage_Core_Model_App_Emulation */
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(Mage_Core_Model_App::ADMIN_STORE_ID, Mage_Core_Model_App_Area::AREA_ADMINHTML);

        try {
            /** @var Blackbox_Notification_Model_Validator $validator */
            $validator = Mage::getSingleton('blackbox_notification/validator');
            $validator->processNotification(self::NOTIFICATION_TYPE_MULTIINVOICE, null, [
                'invoice' => $this
            ]);
        } finally {
            // Stop store emulation process
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
        }

        return true;
    }

    protected function _findItem(Mage_Sales_Model_Order_Invoice_Item $item)
    {
        foreach ($this->items as $_item) {
            if ($_item->getProductId() == $item->getProductId()) {
                return $_item;
            }
        }

        return false;
    }
}