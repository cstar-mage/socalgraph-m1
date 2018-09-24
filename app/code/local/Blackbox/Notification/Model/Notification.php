<?php

/**
 * @method $this setContent(string $value)
 * @method string getContent()
 * @method $this setRuleId(int $value)
 * @method int getRuleId()
 * @method $this setCustomerId(int $value)
 * @method int getCustomerId()
 *
 * Class Blackbox_Notification_Model_Notification
 */
class Blackbox_Notification_Model_Notification extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('blackbox_notification/notification');
    }

    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $this->setCustomerId($customer->getId());
        return $this->setData('customer', $customer);
    }

    public function setRule(Blackbox_Notification_Model_Notification_Rule $rule)
    {
        $this->setRuleId($rule->getId());
        return $this->setData('rule', $rule);
    }

    /**
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (!($customer = $this->getData('customer'))) {
            $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
            $this->setData('customer', $customer);
        }
        return $customer;
    }

    /**
     * @return Blackbox_Notification_Model_Notification_Rule
     */
    public function getRule()
    {
        if (!($rule = $this->getData('rule'))) {
            $rule = Mage::getModel('blackbox_notification/notification_rule')->load($this->getRuleId());
            $this->setData('rule', $rule);
        }
        return $rule;
    }
}