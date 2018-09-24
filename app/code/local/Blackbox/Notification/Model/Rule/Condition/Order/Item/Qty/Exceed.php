<?php

/**
 * Order item qty exceed stock qty rule condition data model
 *
 * @package Blackbox_Notification
 */
class Blackbox_Notification_Model_Rule_Condition_Order_Item_Qty_Exceed extends Mage_Rule_Model_Condition_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('blackbox_notification/rule_condition_order_item_qty_exceed');
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
        /** @var Mage_Sales_Model_Order_Item $orderItem */
        $orderItem = ($object instanceof Mage_Sales_Model_Order_Item) ? $object : $object->getOrderItem();
        if (!($orderItem instanceof Mage_Sales_Model_Order_Item)) {
            $orderItem = Mage::getModel('sales/order_item')->load($object->getOrderItemId());
        }

        $orderItemQty = $orderItem->getQtyOrdered();
        $stockQty = $object->getProduct()->getStockItem()->getQty();

        return $orderItemQty > $stockQty;
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() . Mage::helper('blackbox_notification')->__("Order item's qty exceed stock qty");
        if ($this->getId() != '1') {
            $html.= $this->getRemoveLinkHtml();
        }
        return $html;
    }
}
