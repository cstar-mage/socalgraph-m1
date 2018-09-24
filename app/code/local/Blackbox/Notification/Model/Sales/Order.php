<?php

class Blackbox_Notification_Model_Sales_Order extends Blackbox_OrderApproval_Model_Sales_Order
{
    /**
     * Queue email with new order data
     *
     * @param bool $forceMode if true then email will be sent regardless of the fact that it was already sent previously
     *
     * @return Mage_Sales_Model_Order
     * @throws Exception
     */
    public function queueNewOrderEmail($forceMode = false)
    {
        $storeId = $this->getStore()->getId();

        // Start store emulation process
        /** @var $appEmulation Mage_Core_Model_App_Emulation */
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($this->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();

            $params = array(
                'order' => $this,
                'billing' => $this->getBillingAddress(),
                'payment_html' => $paymentBlockHtml
            );

            $validator = Mage::getSingleton('blackbox_notification/validator');
            /* @var Blackbox_Notification_Model_Validator $validator */
            $result = $validator->processSalesEmail($this, $params, Blackbox_Notification_Model_Rule::TYPE_ORDER_EMAIL, $this->getCustomerEmail());

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
     * Queue email with order update information
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @param bool $forceMode if true then email will be sent regardless of the fact that it was already sent previously
     *
     * @return Mage_Sales_Model_Order
     */
    public function queueOrderUpdateEmail($notifyCustomer = true, $comment = '', $forceMode = false)
    {
        $params = array(
            'order'   => $this,
            'comment' => $comment,
            'billing' => $this->getBillingAddress()
        );

        $validator = Mage::getSingleton('blackbox_notification/validator'); /* @var Blackbox_Notification_Model_Validator $validator */
        $result = $validator->processSalesEmail($this, $params, Blackbox_Notification_Model_Rule::TYPE_ORDER_UPDATE_EMAIL, $this->getCustomerEmail());

        if ($result) {
            $this->setEmailSent(true);
            $this->_getResource()->saveAttribute($this, 'email_sent');
        }

        return $this;
    }
}