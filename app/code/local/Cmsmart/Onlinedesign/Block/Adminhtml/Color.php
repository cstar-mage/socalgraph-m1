<?php
class Cmsmart_Onlinedesign_Block_Adminhtml_Color extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_color';
    $this->_blockGroup = 'onlinedesign';
    $this->_headerText = Mage::helper('onlinedesign')->__('Color Manager');
    $this->_addButtonLabel = Mage::helper('onlinedesign')->__('Add Color');
    parent::__construct();
  }
}