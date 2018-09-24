<?php

class Blackbox_Notification_Model_Sales_Order_Shipment extends Mage_Sales_Model_Order_Shipment
{
    /**
     * Send email with shipment data
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function sendEmail($notifyCustomer = true, $comment = '')
    {
        $order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        // Start store emulation process
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();

            $params = array(
                'order' => $order,
                'shipment' => $this,
                'comment' => $comment,
                'billing' => $order->getBillingAddress(),
                'payment_html' => $paymentBlockHtml
            );

            $validator = Mage::getSingleton('blackbox_notification/validator');
            /* @var Blackbox_Notification_Model_Validator $validator */
            $validator->processSalesEmail($this, $params, Blackbox_Notification_Model_Rule::TYPE_SHIPMENT_EMAIL, $notifyCustomer ? $order->getCustomerEmail() : null);
        } finally {
            // Stop store emulation process
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
        }

        return $this;
    }

    /**
     * Send email with shipment update information
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function sendUpdateEmail($notifyCustomer = true, $comment = '')
    {
        $order = $this->getOrder();

        $params = array(
            'order'    => $order,
            'shipment' => $this,
            'comment'  => $comment,
            'billing'  => $order->getBillingAddress()
        );

        $validator = Mage::getSingleton('blackbox_notification/validator'); /* @var Blackbox_Notification_Model_Validator $validator */
        $validator->processSalesEmail($this, $params, Blackbox_Notification_Model_Rule::TYPE_SHIPMENT_UPDATE_EMAIL, $notifyCustomer ? $order->getCustomerEmail() : null);

        return $this;
    }
}