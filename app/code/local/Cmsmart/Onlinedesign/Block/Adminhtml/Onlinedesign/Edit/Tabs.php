<?php

class Cmsmart_Onlinedesign_Block_Adminhtml_Onlinedesign_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
          'content'   => $this->getLayout()->createBlock('onlinedesign/adminhtml_onlinedesign_edit_tab_form')->toHtml(),
      ));
	  
	  $this->addTab('setting_section', array(
          'label'     => Mage::helper('onlinedesign')->__('Design Settings'),
          'title'     => Mage::helper('onlinedesign')->__('Design Settings'),
          'content'   => $this->getLayout()->createBlock('onlinedesign/adminhtml_onlinedesign_edit_tab_form')->setTemplate('onlinedesign/nbdesigner-box-design-setting.phtml')->toHtml(),
      ));
	  
	  $this->addTab('template_section', array(
		'label'     => Mage::helper('onlinedesign')->__("Manage Templates"),
		'url'       => $this->getUrl('*/*/operator', array('_current' => true)),
		'class'     => 'ajax',
	  ));
     
      return parent::_beforeToHtml();
  }
}