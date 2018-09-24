<?php

class Blackbox_Notification_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_redirectCache = array();

    /**
     * @return Blackbox_Notification_Model_Email_Redirect
     */
    public function getEmailRedirect(Mage_Sales_Model_Order $order, Blackbox_Notification_Model_Rule $rule, $type = Blackbox_Notification_Model_Email_Redirect::TYPE_ORDER_SUMMARY)
    {
        $key = 'redirect_' . $type . '_' . $order->getId() . '_' . $rule->getId();

        if (!$this->_redirectCache[$key]) {
            /** @var Blackbox_Notification_Model_Email_Redirect $redirect */
            $redirect = Mage::getModel('blackbox_notification/email_redirect');

            $redirect->setType($type);
            $redirect->setParams(array('order_id' => $order->getId()));
            $redirect->importConfig($rule);

            $this->_redirectCache[$key] = $redirect;
        }

        return $this->_redirectCache[$key];
    }
}