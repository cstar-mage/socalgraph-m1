<?php
class Wizkunde_WebSSO_Block_Adminhtml_Idp_Edit_Form_Renderer_Fieldset_Claimgrid extends Varien_Data_Form_Element_Abstract{
    protected $_element;

    public function getElementHtml()
    {
        return Mage::helper('core')->getLayout()->createBlock('websso/adminhtml_idp_edit_form_renderer_fieldset_claim_grid')->toHtml();
    }
}