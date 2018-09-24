<?php

/**
 * Empty rule condition data model
 *
 * @package Blackbox_Notification
 */
class Blackbox_Notification_Model_Rule_Condition_Blank extends Mage_Rule_Model_Condition_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('blackbox_notification/rule_condition_blank');
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
        return true;
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml();
        if ($this->getId() != '1') {
            $html.= $this->getRemoveLinkHtml();
        }
        return $html;
    }
}
