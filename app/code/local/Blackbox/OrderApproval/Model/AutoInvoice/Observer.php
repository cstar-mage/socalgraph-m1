<?php

class Blackbox_OrderApproval_Model_AutoInvoice_Observer extends Plumrocket_AutoInvoice_Model_Observer
{
    public function salesOrderSaveCommitAfter(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('autoinvoice')->moduleEnabled()){
            return $this;
        }

        $order = $observer->getEvent()->getOrder();
        if ($order->getIsAutoinvoiced()) {
            return $this;
        }

        if ( ($order->getState() == Mage_Sales_Model_Order::STATE_NEW || $order->getState() == Mage_Sales_Model_Order::STATE_PROCESSING)  && !$order->getIsProcessedByMCornerOrdersObserver()  ) {
//            $orders = Mage::getModel('sales/order_invoice')->getCollection()->addAttributeToFilter('order_id', array('eq' => $order->getId()));
//            $orders->getSelect()->limit(1);
//            if ((int)$orders->count() !== 0) {
//                return $this;
//            }

            if (!Mage::helper('order_approval')->isOrderApproved($order)) {
                return $this;
            }

            return parent::salesOrderSaveCommitAfter($observer);
        }
    }
}