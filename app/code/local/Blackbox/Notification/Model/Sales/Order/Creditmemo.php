<?php

class Blackbox_Notification_Model_Sales_Order_Creditmemo extends Mage_Sales_Model_Order_Creditmemo
{
    /**
     * Send email with creditmemo data
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return Mage_Sales_Model_Order_Creditmemo
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

            // Retrieve corresponding email template id and customer name

            $params = array(
                'order' => $order,
                'creditmemo' => $this,
                'comment' => $comment,
                'billing' => $order->getBillingAddress(),
                'payment_html' => $paymentBlockHtml
            );

            $validator = Mage::getSingleton('blackbox_notification/validator');
            /* @var Blackbox_Notification_Model_Validator $validator */
            $result = $validator->processSalesEmail($this, $params, Blackbox_Notification_Model_Rule::TYPE_CREDITMEMO_EMAIL, $notifyCustomer ? $order->getCustomerEmail() : null);

            if ($result) {
                $this->setEmailSent(true);
                $this->_getResource()->saveAttribute($this, 'email_sent');
            }
        } finally {
            // Stop store emulation process
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
        }

        return $this;
    }

    /**
     * Send email with creditmemo update information
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function sendUpdateEmail($notifyCustomer = true, $comment = '')
    {
        $order = $this->getOrder();

        $params = array(
            'order'      => $order,
            'creditmemo' => $this,
            'comment'    => $comment,
            'billing'    => $order->getBillingAddress()
        );

        $validator = Mage::getSingleton('blackbox_notification/validator'); /* @var Blackbox_Notification_Model_Validator $validator */
        $validator->processSalesEmail($this, $params, Blackbox_Notification_Model_Rule::TYPE_CREDITMEMO_UPDATE_EMAIL, $notifyCustomer ? $order->getCustomerEmail() : null);

        return $this;
    }
}