<?php

class Blackbox_CinemaCloud_Block_SalesRep_Order_History extends Mage_Sales_Block_Order_History
{
    public function __construct()
    {
        Mage_Core_Block_Template::__construct();

        $orders = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('sales_person_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->setOrder('created_at', 'desc')
        ;

        $this->setOrders($orders);
    }

    protected function _prepareLayout()
    {
        $filter = $this->getLayout()->createBlock('cinemacloud/salesRep_order_history_filter', 'salesRep.order.history.filter')
            ->setCollection($this->getOrders());
        $this->setChild('filter', $filter);

        $pager = $this->getLayout()->createBlock('page/html_pager', 'salesRep.order.history.pager')
            ->setCollection($this->getOrders());
        $this->setChild('pager', $pager);
        $this->getOrders()->load();
        return $this;
    }
}