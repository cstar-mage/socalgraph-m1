<?php

/**
 * Customer rule condition data model
 *
 * @category Mage
 * @package Blackbox_RolesPermissions
 */
class Blackbox_RolesPermissions_Model_Rule_Condition_Customer extends Blackbox_RolesPermissions_Model_Rule_Condition_Customer_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('rolespermissions/rule_condition_customer');
    }

    /**
     * Validate Customer Rule Condition
     *
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = ($object instanceof Mage_Customer_Model_Customer) ? $object : $object->getCustomer();
        if (!($customer instanceof Mage_Customer_Model_Customer)) {
            $customer = Mage::getModel('customer/customer')->load($object->getCustomerId());
        }

        return parent::validate($customer);
    }
}
