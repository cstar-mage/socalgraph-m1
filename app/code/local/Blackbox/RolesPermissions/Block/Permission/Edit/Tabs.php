<?php

/**
 * description
 *
 * @package    Blackbox_RolesPermissions
 */
class Blackbox_RolesPermissions_Block_Permission_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('promo_catalog_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('rolespermissions')->__('Permission Rule'));
    }
}
