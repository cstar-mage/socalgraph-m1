<?php

/**
 * Permission rules
 *
 * @package    Blackbox_RolesPermissions
 */

class Blackbox_RolesPermissions_Block_Permission extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'permission';
        $this->_blockGroup = 'rolespermissions';
        $this->_headerText = Mage::helper('rolespermissions')->__('Permissions Rules');
        $this->_addButtonLabel = Mage::helper('rolespermissions')->__('Add New Rule');

        $this->_addButton('apply_product_rules', array(
            'label'     => Mage::helper('rolespermissions')->__('Apply Product Rules'),
            'onclick'   => "location.href='".$this->getUrl('*/*/applyProductRules')."'",
            'class'     => '',
        ));

        $this->_addButton('apply_category_rules', array(
            'label'     => Mage::helper('rolespermissions')->__('Apply Category Rules'),
            'onclick'   => "location.href='".$this->getUrl('*/*/applyCategoryRules')."'",
            'class'     => '',
        ));

        parent::__construct();
    }
}
