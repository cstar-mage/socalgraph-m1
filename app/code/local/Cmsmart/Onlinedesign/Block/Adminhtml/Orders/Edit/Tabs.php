<?php

class Cmsmart_Onlinedesign_Block_Adminhtml_Orders_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('onlinedesign_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('onlinedesign')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('onlinedesign')->__('General Information'),
          'title'     => Mage::helper('onlinedesign')->__('General Information'),
          'content'   => $this->getLayout()->createBlock('onlinedesign/adminhtml_orders_edit_tab_form')->toHtml(),
      ));
	  
	  /* $this->addTab('setting_section', array(
          'label'     => Mage::helper('onlinedesign')->__('Detail Information'),
          'title'     => Mage::helper('onlinedesign')->__('Detail Information'),
          'content'   => $this->getLayout()->createBlock('onlinedesign/adminhtml_orders_edit_tab_form')->setTemplate('onlinedesign/order_detail.phtml')->toHtml(),
      )); */
     
      return parent::_beforeToHtml();
  }
}