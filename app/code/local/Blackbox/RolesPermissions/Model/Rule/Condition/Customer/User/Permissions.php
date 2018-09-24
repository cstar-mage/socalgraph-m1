<?php

class Blackbox_RolesPermissions_Model_Rule_Condition_Customer_User_Permissions extends Mage_Rule_Model_Condition_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('rolespermissions/rule_condition_customer_user_permissions');
    }

    /**
     * Validate Stock Item Qty Rule Condition
     *
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = ($object instanceof Mage_Customer_Model_Customer) ? $object : $object->getCustomer();

        $result = Mage::helper('rolespermissions')->hasPermission($this->getValueParsed(), $customer);

        if ($this->getOperatorForValidate() == '!=') {
            $result = !$result;
        }

        return $result;
    }

    public function setValueName($value)
    {
        return parent::setValueName($value);
    }

    public function getValueElementType()
    {
        return 'text';
    }

    public function getDefaultOperatorOptions($operator = null)
    {
        return array(
            '!=' => 'hasn\'t',
            '==' => 'has'
        );
    }

    public function getDefaultOperatorInputByType()
    {
        return array('string' => array('!=', '=='));
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() . Mage::helper('blackbox_notification')->__("Customer %s permission: %s", $this->getOperatorElement()->getHtml(), $this->getValueElement()->getHtml());
        if ($this->getId() != '1') {
            $html.= $this->getRemoveLinkHtml();
        }
        return $html;
    }
}