<?php

class Cmsmart_Onlinedesign_Block_Adminhtml_Catart_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'onlinedesign';
        $this->_controller = 'adminhtml_catart';
        
        $this->_updateButton('save', 'label', Mage::helper('onlinedesign')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('onlinedesign')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('productdesign_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'productdesign_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'productdesign_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('cat_data') && Mage::registry('cat_data')->getId() ) {
            return Mage::helper('onlinedesign')->__("Edit Category '%s'", $this->htmlEscape(Mage::registry('cat_data')->getTitle()));
        } else {
            return Mage::helper('onlinedesign')->__('Add Category');
        }
    }
}