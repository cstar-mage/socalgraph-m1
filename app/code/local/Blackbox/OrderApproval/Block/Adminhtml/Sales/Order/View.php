<?php

class Blackbox_OrderApproval_Block_Adminhtml_Sales_Order_View extends Blackbox_Checkout_Block_Adminhtml_Sales_Order_View//Mage_Adminhtml_Block_Sales_Order_View
{
    public function __construct()
    {
        parent::__construct();
        $order = $this->getOrder();

        $canApprove = $order->canApprove();

        if ($this->_isAllowedAction('approve') && $canApprove) {
            $_label = Mage::helper('sales')->__('Approve');
            $this->_addButton('order_approve', array(
                'label'     => $_label,
                'onclick'   => 'setLocation(\'' . $this->getApproveUrl() . '\')',
                'class'     => 'go'
            ));
        }

        if ($this->_isAllowedAction('disapprove') && $canApprove) {
            $_label = Mage::helper('sales')->__('Disapprove');
            $this->_addButton('order_disapprove', array(
                'label'     => $_label,
                'onclick'   => 'setLocation(\'' . $this->getDisapproveUrl() . '\')',
                'class'     => 'go'
            ));
        }
    }

    protected function _isAllowedAction($action)
    {
        if ($action == 'invoice') {
            if (!Mage::helper('order_approval')->hasItemsCanInvoice($this->getOrder())) {
                return false;
            }
        }
        return parent::_isAllowedAction($action);
    }

    public function getApproveUrl()
    {
        return $this->getUrl('orderapproval/adminhtml_order_approval/start');
    }

    public function getDisapproveUrl()
    {
        return $this->getUrl('orderapproval/adminhtml_order_disapproval/start');
    }
}