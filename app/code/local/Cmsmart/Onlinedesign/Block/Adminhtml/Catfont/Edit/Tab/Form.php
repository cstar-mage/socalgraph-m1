<?php

class Cmsmart_Onlinedesign_Block_Adminhtml_Catfont_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('cat_form', array('legend'=>Mage::helper('onlinedesign')->__('Category information')));
	 
		$designLoad = Mage::getModel('onlinedesign/catfont')->load(Mage::registry('cat_data')->getId());
		$art_image_path = $designLoad->getFilename();

      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('onlinedesign')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
		
	  /* $fieldset->addField('mycolor', 'text', array(
          'label'     => Mage::helper('onlinedesign')->__('Color'),
          'name'      => 'mycolor',
		  'class'     => 'color {required:false, adjust:false, hash:true}',
          'value'     => Cmsmart_Productdesign_Block_Adminhtml_Renderer_Color
      )); */

	  /* $fieldset->addType('mycolor', 'Cmsmart_Productdesign_Block_Adminhtml_Renderer_Color');
	  
	  $fieldset->addField(
            'mycolor',
            'mycolor',
            array(
                'label'     => Mage::helper('productdesign')->__("Color"),
                'required'  => true,
                'name'      => 'mycolor',
                'class'     => 'color {required:false, adjust:false, hash:true}',
                'value'     => $html
            )
      ); */
		
      if ( Mage::getSingleton('adminhtml/session')->getCatData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCatData());
          Mage::getSingleton('adminhtml/session')->setCatData(null);
      } elseif ( Mage::registry('cat_data') ) {
          $form->setValues(Mage::registry('cat_data')->getData());
      }
	  
      return parent::_prepareForm();
  }
}