<?php

class Cmsmart_Onlinedesign_Block_Adminhtml_Onlinedesign_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      
	  $fieldset = $form->addFieldset('onlinedesign_form', array('legend'=>Mage::helper('onlinedesign')->__('General Design information')));
   
	  $pid     = $this->getRequest()->getParam('id');
	  $product = Mage::getModel('catalog/product')->load($pid);
	  $str_p = '<strong><a href="' . $this->getUrl('adminhtml/catalog_product/edit', array('id' => $pid)) . '" onclick="this.target=\'blank\'">'. $product->getName() . '</a></strong>';
		
	  $fieldset->addField('product', 'note', array(
          'label'     => Mage::helper('onlinedesign')->__('Product'),
          'text'      => $str_p,
      ));
	  
	  $fieldset->addField('sku', 'note', array(
          'label'     => Mage::helper('onlinedesign')->__('Sku'),
          'text'      => '<strong>'.$product->getSku().'</strong>',
      ));
	  
	  /* $add_template_url = $add_template_url = $product->getProductUrl();
	  $add_template_url .= "?product_id=".$product->getId()."&priority=extra&task=create_template";
	  
	  $fieldset->addField('add_template', 'note', array(
          'label'     => Mage::helper('onlinedesign')->__('Add Template'),
          'text'      => '<a href="'.$add_template_url.'" target="_blank">Add Template</a>',
      )); */
     
	  //$fieldset = $form->addFieldset('onlinedesign_form_paramenters', array('legend'=>Mage::helper('onlinedesign')->__('Setting Design information')));
	  
      if ( Mage::getSingleton('adminhtml/session')->getOnlinedesignData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getOnlinedesignData());
          Mage::getSingleton('adminhtml/session')->setOnlinedesignData(null);
      } elseif ( Mage::registry('onlinedesign_data') ) {
          $form->setValues(Mage::registry('onlinedesign_data')->getData());
      }
      return parent::_prepareForm();
  }
}