<?php

class Blackbox_CinemaCloud_Customer_CsrController extends Mage_Core_Controller_Front_Action
{
    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::helper('customer')->getLoginUrl();

        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Account'));
        $this->renderLayout();
    }

    public function orderShowMoreHtmlAction()
    {
        $html = $this->loadLayout('customer_csr_ajaxshowmore')->getLayout()->getBlock('csr.order.history.items')->toHtml();
        $this->getResponse()->setBody($html);
    }

    public function ajaxOrderInfoHtmlAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderId);
        Mage::register('current_order', $order);

        $html = $this->loadLayout('customer_csr_ajaxorderinfo')->getLayout()->getBlock('csr.order.info')->toHtml();
        $this->getResponse()->setBody($html);
    }
}