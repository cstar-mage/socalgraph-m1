<?php

class Cmsmart_Onlinedesign_Block_Adminhtml_Font_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('font_form', array('legend'=>Mage::helper('onlinedesign')->__('Font information')));
		
	  $fieldset->addField('note', 'note', array(
          'label'     => Mage::helper('onlinedesign')->__(''),
          'name'      => 'note',
		  'text'     => '<b>Choose file upload</b><br />
						Supported file .woff, .ttf<br />
						',
      ));
	  
	  $fieldset->addField('filename', 'image', array(
          'label'     => Mage::helper('onlinedesign')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
	  
	  $cates = array();
      $cates[''] = '-- Please select cateogry font --';
	  $collection = Mage::getModel('onlinedesign/catfont')->getCollection();
	  foreach ($collection as $cat) {
		 	$cates[$cat->getId()] = $cat->getTitle();
	  }
      $fieldset->addField('category', 'select', array(
          'label'     => Mage::helper('onlinedesign')->__('Cateogry'),
          'name'      => 'category',
      	  'class'     => 'required-entry',
          'required'  => true,
          'values'    => $cates
      ));
	  
	  $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('onlinedesign')->__('Title'),
          'required'  => false,
          'name'      => 'title',
      ));
		
     
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