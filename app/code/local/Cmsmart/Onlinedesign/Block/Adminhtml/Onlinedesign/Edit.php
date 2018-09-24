<?php

class Cmsmart_Onlinedesign_Block_Adminhtml_Onlinedesign_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
               
        $this->_objectId = 'id';
        $this->_blockGroup = 'onlinedesign';
        $this->_controller = 'adminhtml_onlinedesign';
        
        $this->_updateButton('save', 'label', Mage::helper('onlinedesign')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('onlinedesign')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('onlinedesign_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'onlinedesign_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'onlinedesign_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
		$this->_removeButton('delete');
    }

    public function getHeaderText()
    {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('catalog/product')->load($id);
		if($model) {
			return Mage::helper('onlinedesign')->__("Edit Design Product: '%s'", $this->htmlEscape($model->getName()));
		} else {
			return Mage::helper('onlinedesign')->__('Add Item');
		}
		
        // if( Mage::registry('onlinedesign_data') && Mage::registry('onlinedesign_data')->getId() ) {
            // return Mage::helper('onlinedesign')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('onlinedesign_data')->getTitle()));
        // } else {
            // return Mage::helper('onlinedesign')->__('Add Item');
        // }
    }
}