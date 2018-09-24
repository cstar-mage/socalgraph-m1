<?php

class Blackbox_Checkout_Model_Observer
{
    protected $_quoteItems;

    public function salesQuoteProductAddAfter($observer)
    {
        $this->_quoteItems = $observer->getItems();
    }

    public function getQuoteItems()
    {
        return $this->_quoteItems;
    }

    public function processPreorder($observer)
    {
        $order = $observer->getOrder();
        if ($order->getState() == 'metaorder' || $order->getPreorderPassed() || !$order->getEventDate() && !$order->getShippingDate() || $order->getStatus() == Blackbox_Checkout_Helper_Preorder::STATUS_PREORDER && $order->getState() == Blackbox_Checkout_Helper_Preorder::STATE_PREORDER) {
            return;
        }

        /** @var Blackbox_Checkout_Helper_Preorder $helper */
        $helper = Mage::helper('blackbox_checkout/preorder');

        if (!$helper->timeHasCome($order)) {
            $order->setOrigState($order->getState())
                ->setOrigStatus($order->getStatus())
                ->setState(Blackbox_Checkout_Helper_Preorder::STATE_PREORDER)
                ->setStatus(Blackbox_Checkout_Helper_Preorder::STATUS_PREORDER);
        }
    }
}