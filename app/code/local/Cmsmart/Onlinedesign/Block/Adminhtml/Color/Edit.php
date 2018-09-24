<?php

class Cmsmart_Onlinedesign_Block_Adminhtml_Color_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'onlinedesign';
        $this->_controller = 'adminhtml_color';
        
        /* $this->_updateButton('save', 'label', Mage::helper('onlinedesign')->__('Save Color'));
        $this->_updateButton('delete', 'label', Mage::helper('onlinedesign')->__('Delete Color')); */
		
		//$this->_removeButton('save');
		$this->_removeButton('delete');
		$this->_removeButton("reset");
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
		
		//$this->_removeButton("saveandcontinue");

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
        if( Mage::registry('color_data') && Mage::registry('color_data')->getId() ) {
            return Mage::helper('onlinedesign')->__("Edit Color '%s'", $this->htmlEscape(Mage::registry('color_data')->getColorName()));
        } else {
            return Mage::helper('onlinedesign')->__('Add Color');
        }
    }
}