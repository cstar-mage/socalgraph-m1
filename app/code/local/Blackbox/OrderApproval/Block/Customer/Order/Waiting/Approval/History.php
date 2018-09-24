<?php

class Blackbox_OrderApproval_Block_Customer_Order_Waiting_Approval_History extends Mage_Sales_Block_Order_History
{
    public function __construct()
    {
        Mage_Core_Block_Template::__construct();
        $this->setTemplate('blackbox/orderapproval/customer/order/waiting/approval/history.phtml');

        $orders = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->addFieldToFilter('status', Blackbox_OrderApproval_Model_Sales_Order::STATUS_WAITING_APPROVAL)
            ->setOrder('created_at', 'desc')
        ;

        $this->setOrders($orders);

        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('sales')->__('My Requests For Approvals'));
    }
}