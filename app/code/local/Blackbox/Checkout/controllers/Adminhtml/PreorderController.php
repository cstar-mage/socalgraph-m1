<?php

class Blackbox_Checkout_Adminhtml_PreorderController extends Mage_Adminhtml_Controller_Action
{
    public function processAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        try {
            $order = Mage::getModel('sales/order')->load($orderId);
            if (!$order->getId()) {
                Mage::throwException('The requested order does not exist.');
            }

            if ($order->getState() != Blackbox_Checkout_Helper_Preorder::STATE_PREORDER || $order->getStatus() != Blackbox_Checkout_Helper_Preorder::STATUS_PREORDER) {
                Mage::throwException('The order is not preordered.');
            }

            $order->setState($order->getOrigState() ?: 'new')->setStatus($order->getOrigStatus() ?: 'pending')->setPreorderPassed(1)->save();
            Mage::getSingleton('admin/session')->addSuccess('Order status was changed.');
        } catch (Exception $e) {
            Mage::getSingleton('admin/session')->addError($e->getMessage());
        }

        if (isset($order) && $order->getId()) {
            $this->_redirect('adminhtml/sales_order/view', ['order_id' => $order->getId()]);
        } else {
            $this->_redirect('adminhtml/sales_order/index');
        }
    }
}