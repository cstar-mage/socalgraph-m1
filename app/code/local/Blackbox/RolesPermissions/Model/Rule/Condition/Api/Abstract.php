<?php
/**
 * Abstract Rule api condition data model
 *
 * @method string getAttribute()
 * @method string getOperator()
 *
 */
abstract class Blackbox_RolesPermissions_Model_Rule_Condition_Api_Abstract extends Mage_Rule_Model_Condition_Abstract
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
        return $this->_validateResource($this->getAttribute(), $object->getCode());
    }

    protected function _validateResource($resource, $code)
    {
        if ($resource == $code) {
            return true;
        }

        foreach ($this->_getAcl()->getResourceChildren($resource) as $resource => $instance) {
            if ($this->_validateResource($resource, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Blackbox_RolesPermissions_Model_Api_Acl
     */
    protected function _getAcl()
    {
        if (!isset($this->_acl)) {
            $this->_acl =  Mage::getResourceModel('api/acl')->loadAcl();
        }
        return $this->_acl;
    }
}
