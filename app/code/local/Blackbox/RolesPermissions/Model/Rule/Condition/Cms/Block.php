<?php

/**
 * Cms Block rule condition data model
 *
 * @package Blackbox_RolesPermissions
 */
class Blackbox_RolesPermissions_Model_Rule_Condition_Cms_Block extends Blackbox_RolesPermissions_Model_Rule_Condition_Cms_Block_Abstract
{
    /**
     * Validate Cms Block Rule Condition
     *
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        /** @var Mage_Cms_Model_Block $cmsBlock */
        $cmsBlock = ($object instanceof Mage_Cms_Model_Block) ? $object : $object->getCmsBlock();
        if (!($cmsBlock instanceof Mage_Cms_Model_Block)) {
            $cmsBlock = Mage::getModel('cms/block')->load($object->getCmsBlockId());
        }

        return parent::validate($cmsBlock);
    }
}
