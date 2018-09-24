<?php

class Blackbox_Notification_Model_OrderApproval_Disapproval extends Blackbox_OrderApproval_Model_Disapproval
{
    /**
     * Send email with disapproval data
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return Blackbox_OrderApproval_Model_Disapproval
     */
    public function sendEmail($notifyCustomer = true, $comment = '')
    {
        $storeId = $this->getStore()->getId();

        // Start store emulation process
        /** @var $appEmulation Mage_Core_Model_App_Emulation */
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            $order = $this->getOrder();

            $params = array(
                'order' => $order,
                'disapproval' => $this,
                'comment' => $comment,
            );

            $validator = Mage::getSingleton('blackbox_notification/validator');
            /* @var Blackbox_Notification_Model_Validator $validator */
            $result = $validator->processSalesEmail($this, $params, Blackbox_Notification_Model_Rule::TYPE_DISAPPROVAL_EMAIL, $notifyCustomer ? $order->getCustomerEmail() : null);

            if ($result) {
                $this->_getResource()->saveEmailSent($this, 1);
            }
        } finally {
            // Stop store emulation process
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
        }

        return $this;
    }

    /**
     * Send email with disapproval update information
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return Blackbox_OrderApproval_Model_Disapproval
     */
    public function sendUpdateEmail($notifyCustomer = true, $comment = '')
    {
        $order = $this->getOrder();

        $params = array(
            'order'        => $order,
            'disapproval'  => $this,
            'comment'      => $comment,
        );

        $validator = Mage::getSingleton('blackbox_notification/validator'); /* @var Blackbox_Notification_Model_Validator $validator */
        $validator->processSalesEmail($this, $params, Blackbox_Notification_Model_Rule::TYPE_DISAPPROVAL_UPDATE_EMAIL, $notifyCustomer ? $order->getCustomerEmail() : null);

        return $this;
    }
}