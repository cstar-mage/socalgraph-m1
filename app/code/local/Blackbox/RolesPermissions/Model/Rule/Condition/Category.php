<?php

/**
 * Category rule condition data model
 *
 * @package Blackbox_RolesPermissions
 */
class Blackbox_RolesPermissions_Model_Rule_Condition_Category extends Blackbox_RolesPermissions_Model_Rule_Condition_Category_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('rolespermissions/rule_condition_category');
    }

    /**
     * Validate Category Rule Condition
     *
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        /** @var Mage_Catalog_Model_Category $category */
        $category = ($object instanceof Mage_Catalog_Model_Category) ? $object : $object->getCategory();
        if (!($category instanceof Mage_Catalog_Model_Category)) {
            $category = Mage::getModel('catalog/category')->load($object->getCategoryId());
        }

        return parent::validate($category);
    }
}
