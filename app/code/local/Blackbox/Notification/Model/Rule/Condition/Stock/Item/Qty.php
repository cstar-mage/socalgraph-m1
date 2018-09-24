<?php

/**
 * Product qty rule condition data model
 *
 * @package Blackbox_Notification
 */
class Blackbox_Notification_Model_Rule_Condition_Stock_Item_Qty extends Mage_Rule_Model_Condition_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('blackbox_notification/rule_condition_stock_item_qty');
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
        $value = $this->getValueParsed();
        return $object->getOrigData('qty') >= $value && $object->getQty() < $value;
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() . Mage::helper('blackbox_notification')->__("Stock item's qty is less then %s", $this->getValueElement()->getHtml());
        if ($this->getId() != '1') {
            $html.= $this->getRemoveLinkHtml();
        }
        return $html;
    }
}
