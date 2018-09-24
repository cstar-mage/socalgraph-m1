<?php

class Cmsmart_Onlinedesign_Block_Adminhtml_Color_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('productdesign_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('onlinedesign')->__('Color Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('onlinedesign')->__('Color Information'),
          'title'     => Mage::helper('onlinedesign')->__('Color Information'),
          'content'   => $this->getLayout()->createBlock('onlinedesign/adminhtml_color_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}