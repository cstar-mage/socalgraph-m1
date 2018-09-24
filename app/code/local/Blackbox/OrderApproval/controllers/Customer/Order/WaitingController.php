<?php

class Blackbox_OrderApproval_Customer_Order_WaitingController extends Mage_Sales_Controller_Abstract
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

    /**
     * Customer order history
     */
    public function listAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('head')->setTitle($this->__('My Orders Waiting Response'));

        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }

    public function cancelOrderAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }

        /* @var Blackbox_OrderApproval_Model_Sales_Order $order */
        $order = Mage::registry('current_order');
        try {
            $order->unhold();
            $order->cancel('The order was cancelled by customer.');
            if ($order->isCanceled()) {
                $order->save();
                Mage::getSingleton('customer/session')->addSuccess('The order was cancelled.');
            } else {
                Mage::getSingleton('customer/session')->addError('Can\'t cancel order.');
                $this->_redirect('*/*/*/', array('order_id' => $order->getId()));
                return;
            }
        } catch (Exception $e) {
            Mage::getSingleton('customer/session')->addError($e->getMessage());
        }

        $this->_redirect('sales/order/view', array('order_id' => $order->getId()));
    }

    public function acceptAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }

        /* @var Mage_Sales_Model_Order $order */
        $order = Mage::registry('current_order');
        try {
            $order->unhold();
            $order->addStatusHistoryComment('Customer has accepted order changes.');
            $order->save();
            Mage::getSingleton('customer/session')->addSuccess('Changes in order were accepted.');
        } catch (Exception $e) {
            Mage::getSingleton('customer/session')->addError($e->getMessage());
        }

        $this->_redirect('sales/order/view', array('order_id' => $order->getId()));
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
            && $order->getStatus() == Blackbox_OrderApproval_Model_Sales_Order::STATUS_WAITING_CUSTOMER
        ) {
            return true;
        }
        return false;
    }
}