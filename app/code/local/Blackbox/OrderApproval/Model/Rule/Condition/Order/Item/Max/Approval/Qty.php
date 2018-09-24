<?php

class Blackbox_OrderApproval_Model_Rule_Condition_Order_Item_Max_Approval_Qty extends Blackbox_OrderApproval_Model_Rule_Condition_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('order_approval/rule_condition_order_item_max_approval_qty');
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() . Mage::helper('order_approval')->__("Max Approval Qty.");
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
        $maxApproval = $object->getProduct()->getMaxApproval();
        if (!$maxApproval) {
            return false;
        }

        return $this->_getItemQty($object) >= $maxApproval;
    }

    protected function _getItemQty(Varien_Object $object)
    {
        if ($object instanceof Mage_Sales_Model_Order_Item) {
            return $object->getQtyOrdered();
        } else {
            return $object->getQty();
        }
    }
}