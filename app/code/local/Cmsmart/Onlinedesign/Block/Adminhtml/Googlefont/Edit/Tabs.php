<?php

class Cmsmart_Onlinedesign_Block_Adminhtml_Googlefont_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('onlinedesign_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('onlinedesign')->__('Google Font Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('onlinedesign')->__('Google Font Information'),
          'title'     => Mage::helper('onlinedesign')->__('Google Font Information'),
          'content'   => $this->getLayout()->createBlock('onlinedesign/adminhtml_googlefont_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}