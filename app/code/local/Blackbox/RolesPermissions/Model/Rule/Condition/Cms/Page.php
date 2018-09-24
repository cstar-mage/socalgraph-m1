<?php

/**
 * Cms Page rule condition data model
 *
 * @package Blackbox_RolesPermissions
 */
class Blackbox_RolesPermissions_Model_Rule_Condition_Cms_Page extends Blackbox_RolesPermissions_Model_Rule_Condition_Cms_Page_Abstract
{
    /**
     * Validate Cms Page Rule Condition
     *
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        /** @var Mage_Cms_Model_Page $cmsPage */
        $cmsPage = ($object instanceof Mage_Cms_Model_Page) ? $object : $object->getCmsPage();
        if (!($cmsPage instanceof Mage_Cms_Model_Page)) {
            $cmsPage = Mage::getModel('cms/page')->load($object->getCmsPageId());
        }

        return parent::validate($cmsPage);
    }
}
