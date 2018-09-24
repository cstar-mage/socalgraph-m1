<?php
/**
 * @method string getAttribute()
 * @method string getOperator()
 *
 */
class Blackbox_RolesPermissions_Model_Rule_Condition_Admin_Price extends Blackbox_RolesPermissions_Model_Rule_Condition_Admin_Abstract
{
    /**
     * Retrieve attribute element
     *
     * @return Varien_Form_Element_Abstract
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    /**
     * Validate admin attribute value for condition
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        return $object->getCode() == 'price';
    }

    public function getAttributeElementHtml()
    {
        return 'View prices';
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