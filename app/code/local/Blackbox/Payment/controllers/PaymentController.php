<?php

class Blackbox_Payment_PaymentController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('customer/account/login');
            return $this;
        }

        return $this;
    }

    public function indexAction()
    {
        $this->_redirect('*/*/create', ['_current' => true]);
    }

    public function createAction()
    {
        if (!$this->_init()) {
            return;
        }
        $this->loadLayout()->renderLayout();
    }

    public function createPostAction()
    {
        if (!$this->_init()) {
            return;
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::registry('current_order');


    }

    protected function _initQuote(Mage_Sales_Model_Order $order)
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getSingleton('checkout/session')->getQuote();

        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach ($quote->getAllItems() as $item) {
            $item->isDeleted(true);
        }

        /** @var Mage_Sales_Model_Convert_Order $converter */
        $converter = Mage::getModel('sales/convert_order');

        $converter->toQuote($order, $quote);

        $copyFields = [
            'subtotal',
            'base_subtotal'
        ];
        foreach ($copyFields as $field) {
            $quote->setDataUsingMethod($field, $order->getDataUsingMethod($field));
        }
        $quote->assignCustomer(Mage::getSingleton('customer/session')->getCustomer());

        $copyFields = [
            'base_price' => 'base_price',
            'price' => 'price',
            'qty_ordered' => 'qty',
            'base_price_incl_tax' => 'base_price_incl_tax',
            'price_incl_tax' => 'price_incl_tax'
        ];
        foreach ($order->getAllItems() as $item) {
            $qItem = $converter->itemToQuoteItem($item)->setQuote($quote);
            foreach ($copyFields as $from => $to) {
                $qItem->setDataUsingMethod($to, $item->getDataUsingMethod($from));
            }
            $quote->addItem($qItem);
        }

        $quote->setBillingAddress($converter->addressToQuoteAddress($order->getBillingAddress()));
        $shippingAddress = $converter->toQuoteShippingAddress($order);
        $copyFields = [
            'tax_amount' => 'tax_amount',
            'base_tax_amount' => 'base_tax_amount',
            'grand_total' => 'grand_total',
            'base_grand_total' => 'base_grand_total',
        ];
        foreach ($copyFields as $from => $to) {
            $shippingAddress->setDataUsingMethod($to, $order->getDataUsingMethod($from));
        }
        $quote->setShippingAddress($shippingAddress);
    }

    protected function _init()
    {
        if (!($order = $this->_initOrder())/* || !($invoice = $this->_initInvoice($order))*/) {
            if ($this->_getRefererUrl()) {
                /** @var Blackbox_CinemaCloud_Helper_Data $helper */
                $helper = Mage::helper('cinemacloud');
                if ($helper->isCustomerSalesRep()) {
                    $defaultUrl = 'customer/salesRep';
                } else if ($helper->isCustomerCSR()) {
                    $defaultUrl = 'customer/csr';
                } else {
                    $defaultUrl = 'customer/account';
                }
                $this->_redirectReferer($defaultUrl);
            }
            return false;
        }

        $this->_initQuote($order);

        return true;
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    protected function _initOrder()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if (!$orderId) {
            return false;
        }

        $order = Mage::getModel('sales/order')->load($orderId);
        if (!$order->getId()) {
            Mage::getSingleton('checkout/session')->addError($this->__('The order no longer exists.'));
            return false;
        }

        Mage::register('current_order', $order);
        return $order;
    }

    /**
     * Initialize invoice model instance
     *
     * @var Mage_Sales_Model_Order $order
     * @return Mage_Sales_Model_Order_Invoice
     */
    protected function _initInvoice(Mage_Sales_Model_Order $order)
    {
        $invoice = false;
        if (!$order->canInvoice() || $order->getTotalInvoiced() > $order->getGrandTotal()) {
            Mage::getSingleton('checkout/session')->addError($this->__('The order does not allow creating an invoice.'));
            return false;
        }
        $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
        if (!$invoice->getTotalQty()) {
            Mage::throwException($this->__('Cannot create an invoice without products.'));
        }

        Mage::register('current_invoice', $invoice);
        return $invoice;
    }
}