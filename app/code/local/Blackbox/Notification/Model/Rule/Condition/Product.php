<?php

class Blackbox_Notification_Model_Rule_Condition_Product
    extends Mage_SalesRule_Model_Rule_Condition_Product
{
    protected function _addSpecialAttributes(array &$attributes)
    {
        parent::_addSpecialAttributes($attributes);
        $attributes['type_id'] = Mage::helper('blackbox_notification')->__('Type');
    }

    /**
     * Retrieve attribute object
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function getAttributeObject()
    {
        if ($this->getAttribute() != 'type_id') {
            return parent::getAttributeObject();
        }

        $obj = Mage::getSingleton('blackbox_notification/product_type_attribute');

        return $obj;
    }
}