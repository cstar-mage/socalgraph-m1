<?php

class Blackbox_SummarInvoice_Model_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('summar_invoice/rule_condition_combine');
    }

    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value' => 'summar_invoice/rule_condition_combine', 'label' => Mage::helper('summar_invoice')->__('Conditions combination')),
            array('value' => 'summar_invoice/rule_condition_monthly', 'label' => Mage::helper('summar_invoice')->__('Is Monthly Report')),
            array('value' => 'summar_invoice/rule_condition_weekly', 'label' => Mage::helper('summar_invoice')->__('Is Weekly Report'))
        ));

        $additional = new Varien_Object();
        Mage::dispatchEvent('summar_invoice_rule_condition_combine', array('additional' => $additional));
        if ($additionalConditions = $additional->getConditions()) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }
}
