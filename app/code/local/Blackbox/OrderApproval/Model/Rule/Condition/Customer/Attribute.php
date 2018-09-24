<?php

/**
 * Customer rule condition data model
 *
 * @package Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Model_Rule_Condition_Customer_Attribute extends Blackbox_OrderApproval_Model_Rule_Condition_Customer_Abstract
{
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
