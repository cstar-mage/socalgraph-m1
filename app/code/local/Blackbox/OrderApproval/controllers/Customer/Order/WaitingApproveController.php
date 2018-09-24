<?php

class Blackbox_OrderApproval_Customer_Order_WaitingApproveController extends Mage_Sales_Controller_Abstract
{
    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $loginUrl = Mage::helper('customer')->getLoginUrl();

        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    public function indexAction()
    {
        $this->_forward('history');
    }

    /**
     * Customer order history
     */
    public function historyAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('head')->setTitle($this->__('Requests For Approval'));

        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }

    public function printAction()
    {
        $this->_forward('print', 'order', 'sales');
    }

    public function printCreditmemoAction()
    {
        $this->_forward('printCreditmemo', 'order', 'sales');
    }

    public function printInvoiceAction()
    {
        $this->_forward('printInvoice', 'order', 'sales');
    }

    public function printShipmentAction()
    {
        $this->_forward('printShipment', 'order', 'sales');
    }

    /**
     * Check order view availability
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  bool
     */
    protected function _canViewOrder($order)
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        if ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerId)
            && $order->getStatus() == Blackbox_OrderApproval_Model_Sales_Order::STATUS_WAITING_APPROVAL
        ) {
            return true;
        }
        return false;
    }
}