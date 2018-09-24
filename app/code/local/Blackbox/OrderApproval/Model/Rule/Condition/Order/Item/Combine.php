<?php

class Blackbox_OrderApproval_Model_Rule_Condition_Order_Item_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('order_approval/rule_condition_order_item_combine');
    }

    public function getNewChildSelectOptions()
    {
        $orderItemCondition = Mage::getModel('order_approval/rule_condition_order_item_attribute');
        $orderItemConditionAttributes = $orderItemCondition->loadAttributeOptions()->getAttributeOption();
        $orderItemAttributes = array();
        foreach ($orderItemConditionAttributes as $code => $label) {
            $orderItemAttributes[] = array('value'=>'order_approval/rule_condition_order_item_attribute|'.$code, 'label'=>$label);
        }

        $productCondition = Mage::getModel('order_approval/rule_condition_order_item_product_attribute');
        $productConditionAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        $productAttributes = array();
        foreach ($productConditionAttributes as $code => $label) {
            $productAttributes[] = array('value'=>'order_approval/rule_condition_order_item_product_attribute|'.$code, 'label'=>$label);
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value' => 'order_approval/rule_condition_order_item_combine', 'label' => Mage::helper('order_approval')->__('Conditions combination')),
            array('label' => Mage::helper('order_approval')->__('Order Item Attribute'), 'value' => $orderItemAttributes),
            array('label' => Mage::helper('order_approval')->__('Product Attribute'), 'value' => $productAttributes),
            array('value' => 'order_approval/rule_condition_order_item_max_approval_qty', 'label' => 'Max Approval Qty'),
        ));

        $additional = new Varien_Object();
        Mage::dispatchEvent('order_approval_rule_condition_order_item_combine', array('additional' => $additional));
        if ($additionalConditions = $additional->getConditions()) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }
}
