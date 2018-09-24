<?php

class Blackbox_RolesPermissions_Model_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('rolespermissions/rule_condition_combine');
    }

    public function getNewChildSelectOptions()
    {
        $addressCondition = Mage::getModel('rolespermissions/rule_condition_customer');
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($addressAttributes as $code=>$label) {
            $attributes[] = array('value'=>'rolespermissions/rule_condition_customer|'.$code, 'label'=>$label);
        }
        $attributes[] = array('value' => 'rolespermissions/rule_condition_customer_user_permissions', 'label' => 'User Permission');

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value'=>'rolespermissions/rule_condition_combine', 'label'=>Mage::helper('rolespermissions')->__('Conditions combination')),
            array('label'=>Mage::helper('rolespermissions')->__('Customer Attribute'), 'value'=>$attributes),
        ));

        $additional = new Varien_Object();
        Mage::dispatchEvent('rolespermissions_rule_condition_combine', array('additional' => $additional));
        if ($additionalConditions = $additional->getConditions()) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }
}
