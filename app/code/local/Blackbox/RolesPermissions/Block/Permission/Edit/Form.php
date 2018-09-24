<?php

/**
 * description
 *
 * @package    Blackbox_RolesPermissions
 */
class Blackbox_RolesPermissions_Block_Permission_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('permission_form');
        $this->setTitle(Mage::helper('rolespermissions')->__('Rule Information'));
    }
    
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }


}
