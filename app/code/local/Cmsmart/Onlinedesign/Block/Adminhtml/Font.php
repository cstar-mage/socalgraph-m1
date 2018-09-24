<?php
class Cmsmart_Onlinedesign_Block_Adminhtml_Font extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_font';
    $this->_blockGroup = 'onlinedesign';
    $this->_headerText = Mage::helper('onlinedesign')->__('Custom Font Manager');
    $this->_addButtonLabel = Mage::helper('onlinedesign')->__('Add Custom Font');
    parent::__construct();
  }
}