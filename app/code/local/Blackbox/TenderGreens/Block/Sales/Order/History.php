<?php

class Blackbox_TenderGreens_Block_Sales_Order_History extends Mage_Sales_Block_Order_History
{
    protected function _prepareLayout()
    {
        Mage_Core_Block_Template::_prepareLayout();

        $filter = $this->getLayout()->createBlock('tendergreens/sales_order_history_filter', 'sales.order.history.filter')
            ->setCollection($this->getOrders());
        $this->setChild('filter', $filter);

        $pager = $this->getLayout()->createBlock('page/html_pager', 'sales.order.history.pager')
            ->setCollection($this->getOrders());
        $this->setChild('pager', $pager);
        $this->getOrders()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getFilterHtml()
    {
        return $this->getChildHtml('filter');
    }

    public function getViewUrl($order)
    {
        return $this->getUrl('sales/order/view', array('order_id' => $order->getId()));
    }

    public function getTrackUrl($order)
    {
        return $this->getUrl('sales/order/track', array('order_id' => $order->getId()));
    }

    public function getReorderUrl($order)
    {
        return $this->getUrl('sales/order/reorder', array('order_id' => $order->getId()));
    }

    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}