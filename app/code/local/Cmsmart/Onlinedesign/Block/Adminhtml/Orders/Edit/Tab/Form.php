<?php

class Cmsmart_Onlinedesign_Block_Adminhtml_Orders_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      
	  $fieldset = $form->addFieldset('onlinedesign_form', array('legend'=>Mage::helper('onlinedesign')->__('Order Information')));
     
	  $order_id     = $this->getRequest()->getParam('order_id');
	  $order = Mage::getModel('sales/order')->load($order_id);
      $increment_id = $order->getIncrementId();
	  $str_order = '<strong><a href="' . $this->getUrl('adminhtml/sales_order/view', array('order_id' => $order_id)) . '" onclick="this.target=\'blank\'">#'  
      					 . $increment_id . '</a>' . " (\${$order->getGrandTotal()}) "."</strong>";
		
	  $purchase_on = "<strong>".Mage::helper('onlinedesign/data')->locale_time_format(Mage::getModel('core/date')->timestamp($order->getCreatedAt()),Mage_Core_Model_Locale::FORMAT_TYPE_FULL)."</strong>";
	  $email_order = Mage::helper('onlinedesign')->getLinkCustomer($order->getCustomerId(), $order->getCustomerEmail());

	  $fieldset->addField('Order ID', 'note', array(
          'label'     => Mage::helper('onlinedesign')->__('Order Increment ID'),
          'text'      => $str_order,
      ));
	  
	  $fieldset->addField('Purchased On', 'note', array(
          'label'     => Mage::helper('onlinedesign')->__('Purchased On'),
          'text'      => $purchase_on,
      ));
	  
	  $fieldset->addField('Customer Email', 'note', array(
          'label'     => Mage::helper('onlinedesign')->__('Customer Email'),
          'text'      => $email_order,
      ));
	  
	  $fieldset = $form->addFieldset('design_img_form', array('legend'=>Mage::helper('onlinedesign')->__('Designs Information')));
	  
	  // Setting custom renderer for content field to remove label column
      $renderer = $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset_element')
            ->setTemplate('onlinedesign/view_order.phtml');
      $fieldset->setRenderer($renderer);
	  
	  /* $fieldset = $form->addFieldset('design_sendmail_form', array('legend'=>Mage::helper('onlinedesign')->__('Designs Information')));
      $renderer = $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset_element')
            ->setTemplate('onlinedesign/design_sendmail.phtml');
      $fieldset->setRenderer($renderer); */
	  
      /* $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('onlinedesign')->__('Enabled Desgin'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('onlinedesign')->__('Yes'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('onlinedesign')->__('No'),
              ),
          ),
      )); */
	  
	  /* $fieldset->addField('dpi', 'text', array(
          'label'     => Mage::helper('onlinedesign')->__('Dpi'),
          'required'  => false,
          'name'      => 'dpi',
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