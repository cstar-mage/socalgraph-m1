<?php

class Blackbox_OrderApproval_Block_Customer_Disapprovals_List extends Mage_Core_Block_Template
{
    public function _construct()
    {
        parent::_construct();

        $session = Mage::getSingleton('customer/session');
        $disappovals = Mage::getResourceModel('order_approval/disapproval_collection')
            ->addFieldToFilter('customer_id', $session->getCustomerId())
            ->addStatusFilter(Blackbox_OrderApproval_Model_Disapproval::STATE_OPEN)
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addOrder('entity_id', 'DESC');

        $this->setItems($disappovals);
    }

    /**
     * @return Blackbox_Tradeshow_Block_Customer_Products_List
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'order_approval.customer.disapprovals.pager')
            ->setCollection($this->getItems());
        $this->setChild('pager', $pager);
        $this->getItems()->load();
        return $this;
    }

    /**
     * Return order view url
     *
     * @param integer $orderId
     * @return string
     */
    public function getOrderViewUrl($orderId)
    {
        return $this->getUrl('sales/order/view', array('order_id' => $orderId));
    }

    /**
     * Disapproval view url
     *
     * @param Blackbox_OrderApproval_Model_Disapproval $disapproval
     * @return string
     */
    public function getDisapprovalViewUrl($disapproval)
    {
        return $this->getUrl('order_approval/customer_disapproval/view', array('disapproval_id' => $disapproval->getId()));
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/');
    }

    /**
     * @return bool
     */
    public function getIsOpenInNewWindow()
    {
        return false;
    }
}