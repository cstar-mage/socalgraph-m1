<?php

/**
 * Order Item rule condition data model
 *
 * @package Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Model_Rule_Condition_Order_Item_Attribute extends Blackbox_OrderApproval_Model_Rule_Condition_Order_Item_Abstract
{
    /**
     * Validate Order Item Rule Condition
     *
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        /** @var Mage_Sales_Model_Order_Item $item */
        $item = ($object instanceof Mage_Sales_Model_Order_Item || $object instanceof Mage_Sales_Model_Quote_Item) ? $object : $object->getOrderItem();
        if (!($item instanceof Mage_Sales_Model_Order_Item) && !($item instanceof Mage_Sales_Model_Quote_Item) ) {
            $item = Mage::getModel('sales/order_item')->load($object->getOrderItemId());
        }

        return parent::validate($item);
    }
}
