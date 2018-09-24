<?php

/**
 * Api rule condition data model
 *
 * @package Blackbox_RolesPermissions
 */
class Blackbox_RolesPermissions_Model_Rule_Condition_Api extends Blackbox_RolesPermissions_Model_Rule_Condition_Api_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('rolespermissions/rule_condition_api');
    }
    /**
     * Validate Admin Rule Condition
     *
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        return parent::validate($object);
    }

    public function getAttributeElement()
    {
        if (is_null($this->getAttribute())) {
            foreach ($this->getAttributeOption() as $k => $v) {
                $this->setAttribute($k);
                break;
            }
        }
        return $this->getForm()->addField($this->getPrefix() . '__' . $this->getId() . '__attribute', 'select', array(
            'name'       => 'rule[' . $this->getPrefix() . '][' . $this->getId() . '][attribute]',
            'values'     => $this->getAttributeSelectOptions(),
            'value'      => $this->getAttribute(),
            'value_name' => $this->getAttribute(),
        ))->setRenderer(Mage::getBlockSingleton('rule/editable'))
            ->setShowAsText(true);
    }

    public function asHtml() {
        return parent::asHtml();
    }

    public function getOperatorElementHtml()
    {
        return '';
    }

    public function getValueElementHtml()
    {
        return '';
    }
}
