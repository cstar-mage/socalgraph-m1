<?php

class Cmsmart_Onlinedesign_Block_Adminhtml_Color_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('color_form', array('legend'=>Mage::helper('onlinedesign')->__('Color Information')));

	  $color_load = Mage::getModel('onlinedesign/color')->load(Mage::registry('color_data')->getId());
	  $hex = $color_load->getHex();
	
	  $fieldset->addField('hex', 'text', array(
          'label'     => Mage::helper('onlinedesign')->__('Hex'),
          'class'     => '',
          'required'  => false,
          'name'      => 'hex',
		  'style'	  => 'width: 175px;display: none;',
		  'after_element_html' => '<input id="change_color" class="jscolor" value="'.$hex.'" style="text-align: center;font-size: 15px;height: 23px; width: 278px;">',
      ));
	  
      $fieldset->addField('color_name', 'text', array(
          'label'     => Mage::helper('onlinedesign')->__('Color Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'color_name',
      ));
	  
	  $fieldset2 = $form->addFieldset('add_color_form', array('legend'=>Mage::helper('onlinedesign')->__('Add More Colors')));

	  $fieldset2->setRenderer(
            $this->getLayout()->createBlock('onlinedesign/adminhtml_renderer_display')
        );
	  
      if ( Mage::getSingleton('adminhtml/session')->getColorData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getColorData());
          Mage::getSingleton('adminhtml/session')->setColorData(null);
      } elseif ( Mage::registry('color_data') ) {
          $form->setValues(Mage::registry('color_data')->getData());
      }
	  
      return parent::_prepareForm();
  }
}