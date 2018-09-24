<?php

class Blackbox_Notification_Model_OrderApproval_Approval extends Blackbox_OrderApproval_Model_Approval
{
    /**
     * Send email with approval data
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return Blackbox_OrderApproval_Model_Approval
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
                'approval' => $this,
                'comment' => $comment,
            );

            $validator = Mage::getSingleton('blackbox_notification/validator');
            /* @var Blackbox_Notification_Model_Validator $validator */
            $result = $validator->processSalesEmail($this, $params, Blackbox_Notification_Model_Rule::TYPE_APPROVAL_EMAIL, $notifyCustomer ? $order->getCustomerEmail() : null);

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
     * Send email with approval update information
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return Blackbox_OrderApproval_Model_Approval
     */
    public function sendUpdateEmail($notifyCustomer = true, $comment = '')
    {
        $order = $this->getOrder();

        $params = array(
            'order'        => $order,
            'approval'      => $this,
            'comment'      => $comment,
        );

        $validator = Mage::getSingleton('blackbox_notification/validator'); /* @var Blackbox_Notification_Model_Validator $validator */
        $validator->processSalesEmail($this, $params, Blackbox_Notification_Model_Rule::TYPE_APPROVAL_UPDATE_EMAIL, $notifyCustomer ? $order->getCustomerEmail() : null);

        return $this;
    }
}