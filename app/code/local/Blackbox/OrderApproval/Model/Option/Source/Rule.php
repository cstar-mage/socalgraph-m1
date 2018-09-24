<?php

class Blackbox_OrderApproval_Model_Option_Source_Rule
{
    public function getRules()
    {
        $rules = Mage::getResourceModel('order_approval/rule_collection')
            ->addFieldToSelect('rule_id')
            ->addFieldToSelect('name');

        $result = array();
        foreach ($rules as $rule) {
            $result[$rule->getId()] = $rule->getName();
        }

        return $result;
    }
}