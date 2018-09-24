<?php

class Blackbox_OrderApproval_Block_Customer_Order_Waiting_List extends Mage_Sales_Block_Order_History
{
    public function __construct()
    {
        parent::__construct();

        $orders = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->addFieldToFilter('status', 'waiting_customer')
            ->setOrder('created_at', 'desc')
        ;

        $this->setOrders($orders);
    }

    public function getTrackUrl($order)
    {
        return $this->getUrl('sales/order/track', array('order_id' => $order->getId()));
    }

    public function getReorderUrl($order)
    {
        return $this->getUrl('sales/order/reorder', array('order_id' => $order->getId()));
    }
}