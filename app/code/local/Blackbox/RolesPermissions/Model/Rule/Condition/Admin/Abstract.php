<?php
/**
 * Abstract Rule admin condition data model
 *
 * @method string getAttribute()
 * @method string getOperator()
 *
 */
abstract class Blackbox_RolesPermissions_Model_Rule_Condition_Admin_Abstract extends Mage_Rule_Model_Condition_Abstract
{
    const INCLUDE_CHILDREN = 0;
    const INCLUDE_PARENTS = 1;
    const INCLUDE_ALL = 2;
    const INCLUDE_NONE = 3;

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

    protected function _validateResource($resource, $code, $direction = 0)
    {
        if ($resource == $code) {
            return true;
        }

        if ($direction >= 0 && ($this->getValue() == self::INCLUDE_CHILDREN || $this->getValue() == self::INCLUDE_ALL)) {
            foreach ($this->_getAcl()->getResourceChildren($resource) as $res => $instance) {
                if ($this->_validateResource($res, $code, 1)) {
                    return true;
                }
            }
        }

        if ($direction <=0 && ($this->getValue() == self::INCLUDE_PARENTS || $this->getValue() == self::INCLUDE_ALL)) {
            $parent = $this->_getAcl()->getResourceParent($resource);
            if ($parent && $this->_validateResource($parent->getResourceId(), $code, -1)) {
                return true;
            }
        }

        return false;
    }

    public function getValue()
    {
        $value = parent::getValue();
        if (is_null($value) || $value === '') {
            return self::INCLUDE_CHILDREN;
        }
        return $value;
    }

    public function getValueElementType()
    {
        return 'select';
    }

    public function getValueSelectOptions()
    {
        return [
            ['value' => self::INCLUDE_CHILDREN, 'label' => 'Include Children'],
            ['value' => self::INCLUDE_PARENTS, 'label' => 'Include Parents'],
            ['value' => self::INCLUDE_ALL, 'label' => 'Include All'],
            ['value' => self::INCLUDE_NONE, 'label' => 'Not Include'],
        ];
    }

    /**
     * @return Blackbox_RolesPermissions_Model_Admin_Acl
     */
    protected function _getAcl()
    {
        if (!isset($this->_acl)) {
            $this->_acl = Mage::getSingleton('admin/session')->getAcl();
            if (!$this->_acl) {
                $this->_acl = Mage::getResourceModel('admin/acl')->loadAcl();
            }
        }
        return $this->_acl;
    }
}
