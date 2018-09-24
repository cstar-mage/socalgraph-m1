<?php

class Blackbox_SummarInvoice_Model_Rule_Condition_Weekly extends Mage_Rule_Model_Condition_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('summar_invoice/rule_condition_weekly');

        $this->setValueOption(array('0' => 'No', '1' => 'Yes'));
        $this->setValue('0');
    }

    /**
     * Validate Customer Is Guest
     *
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        return Mage::registry('summar_invoice_weekly_report') ? $this->getValue() == '1' : $this->getValue() != '1';
    }

    public function getValueElementType()
    {
        return 'select';
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() . Mage::helper('summar_invoice')->__("Is Weekly Report: %s", $this->getValueElement()->getHtml());
        if ($this->getId() != '1') {
            $html.= $this->getRemoveLinkHtml();
        }
        return $html;
    }
}