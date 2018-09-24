<?php

class Blackbox_OrderApproval_Model_Rule_Condition_Customer_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('order_approval/rule_condition_customer_combine');
    }

    public function getNewChildSelectOptions()
    {
        $addressCondition = Mage::getModel('order_approval/rule_condition_customer_attribute');
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($addressAttributes as $code=>$label) {
            $attributes[] = array('value'=>'order_approval/rule_condition_customer_attribute|'.$code, 'label'=>$label);
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value'=>'order_approval/rule_condition_customer_combine', 'label'=>Mage::helper('order_approval')->__('Conditions combination')),
            array('label'=>Mage::helper('order_approval')->__('Customer Attribute'), 'value'=>$attributes),
        ));

        $additional = new Varien_Object();
        Mage::dispatchEvent('order_approval_rule_condition_customer_combine', array('additional' => $additional));
        if ($additionalConditions = $additional->getConditions()) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }
}
