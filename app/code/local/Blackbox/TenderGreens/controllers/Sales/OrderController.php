<?php
require_once(Mage::getModuleDir('controllers','Mage_Sales') . DS . 'OrderController.php');

class Blackbox_TenderGreens_Sales_OrderController extends Mage_Sales_OrderController
{
    public function trackingAction()
    {
        if (!$this->_loadValidOrder()) {
            $this->_redirect('sales/order');
            return;
        }

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
}