<?php

class Blackbox_CinemaCloud_Checkout_PaymentController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($orderId);

        if ($order->getTotalInvoiced() >= $order->getGrandTotal()) {
            Mage::throwException('Order already invoiced.');
        }

        $redirectUrl = $order->getPayment()->getMethodInstance()->getOrderPlaceRedirectUrl();
        if (!$redirectUrl) {
            $redirectUrl = $order->getPayment()->getMethodInstance()->getCheckoutRedirectUrl();
        }
        if (!$redirectUrl) {
            Mage::throwException('The payment method cant be paid online.');
        }

        $this->getCheckoutSession()
            ->setLastOrderId($order->getId())
            ->setRedirectUrl($redirectUrl)
            ->setLastRealOrderId($order->getIncrementId());

        $this->_redirectUrl($redirectUrl);
    }

    /**
     * @return Mage_Checkout_Model_Session
     */
    protected function getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }
}