<?php

class Blackbox_OrderApproval_Model_Rule_Condition_Order_Item_Found_Any
    extends Blackbox_OrderApproval_Model_Rule_Condition_Order_Item_Found
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('order_approval/rule_condition_order_item_found_any');
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() . Mage::helper('order_approval')->__("If item in the order match this condition:");
        if ($this->getId() != '1') {
            $html.= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    /**
     * validate
     *
     * @param Varien_Object $object
     * @return boolean
     */
    public function validate(Varien_Object $object)
    {
        if (empty($this->getConditions())) {
            return true;
        }

        $this->setAggregator('any');
        $this->setValue(1);

        if ($object instanceof Mage_Sales_Model_Order_Item || $object instanceof Mage_Sales_Model_Quote_Item_Abstract) {
            $object = (new Varien_Object())->setAllItems(array($object));
        }

        return parent::validate($object);
    }

    /**
     * validate
     *
     * @param Mage_Sales_Model_Order $object
     * @return boolean
     */
    public function getMatchedItemsQty(Varien_Object $object)
    {
        if ($object instanceof Mage_Sales_Model_Order_Item || $object instanceof Mage_Sales_Model_Quote_Item) {
            $object = (new Varien_Object())->setAllItems(array($object));
        }

        $matchedItems = array();
        foreach ($object->getAllItems() as $item) {
            if (!empty($this->getConditions())) {
                $found = false;
                foreach ($this->getConditions() as $cond) {
                    $found = $cond->validate($item);
                }
            } else {
                $found = true;
            }
            if ($found) {
                $matchedItems[$item->getId()] = $this->getItemQty($item);
            }
        }
        return $matchedItems;
    }
}