<?php

class Cmsmart_Onlinedesign_Block_Adminhtml_Googlefont_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);

	  $fieldset = $form->addFieldset('googlefont_form', array('legend'=>Mage::helper('onlinedesign')->__('Google Font information')));
	  
	  // Setting custom renderer for content field to remove label column
      $renderer = $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset_element')
            ->setTemplate('onlinedesign/googlefont.phtml');
      $fieldset->setRenderer($renderer);
		
     
      if ( Mage::getSingleton('adminhtml/session')->getFontData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getFontData());
          Mage::getSingleton('adminhtml/session')->setFontData(null);
      } elseif ( Mage::registry('font_data') ) {
          $form->setValues(Mage::registry('font_data')->getData());
      }
	  
      return parent::_prepareForm();
  }
}