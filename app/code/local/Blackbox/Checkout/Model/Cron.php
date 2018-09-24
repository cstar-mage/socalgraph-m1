<?php

class Blackbox_Checkout_Model_Cron
{
    public function processPreOrders()
    {
        /** @var Mage_Sales_Model_Resource_Order_Collection $collection */
        $collection = Mage::getResourceModel('sales/order_collection');
        $collection->addFieldToFilter('state', Blackbox_Checkout_Helper_Preorder::STATE_PREORDER)
            ->addFieldToFilter('status', Blackbox_Checkout_Helper_Preorder::STATUS_PREORDER);

        /** @var Blackbox_Checkout_Helper_Preorder $helper */
        $helper = Mage::helper('blackbox_checkout/preorder');

        if ($helper->isDebug()) {
            Mage::log('Running cron.', null, 'preorder.log', true);
        }

        /** @var Mage_Sales_Model_Order $order */
        foreach ($collection as $order) {
            if ($helper->timeHasCome($order)) {
                $order->setState($order->getOrigState() ?: 'new')->setStatus($order->getOrigStatus() ?: 'pending')->setPreorderPassed(1)->save();
                if ($helper->isDebug()) {
                    Mage::log('Order #' . $order->getIncrementId() . ' changed status to ' . $order->getStatus(), null, 'preorder.log', true);
                }
            }
        }
    }
}