<?php
require_once(Mage::getModuleDir('controllers','Mage_Sales') . DS . 'OrderController.php');

class Blackbox_TenderGreens_Sales_OrderController extends Mage_Sales_OrderController
{
    public function trackingAction()
    {
        $this->_loadValidOrderBySearch();
        $this->loadLayout();
        $this->initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    public function acceptAction()
    {
        if (!$this->_loadValidOrder()) {
            $this->_redirect('sales/order');
            return;
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::registry('current_order');
        $order->setAccepted(1)->save();

        Mage::getSingleton('customer/session')->addSuccess('The order was accepted.');
        $this->_redirect('*/*/tracking', ['order_id' => $order->getId()]);
    }

    protected function _loadValidOrderBySearch()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if (empty($orderId)) {
            return false;
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order');

        $order->loadByIncrementId($orderId);
        if ($this->_canViewOrder($order)) {
            Mage::register('current_order', $order);
            return true;
        }

        $order->loadByAttribute('epace_job_id', $orderId);
        if ($this->_canViewOrder($order)) {
            Mage::register('current_order', $order);
            return true;
        }

        if (!is_numeric($orderId) || !$orderId) {
            return false;
        }

        $orderId = (int)$orderId;

        $order->load($orderId);

        if ($this->_canViewOrder($order)) {
            Mage::register('current_order', $order);
            return true;
        }
        return false;
    }
}