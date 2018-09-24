<?php
class Cmsmart_Onlinedesign_Block_Adminhtml_Googlefont extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_googlefont';
    $this->_blockGroup = 'onlinedesign';
    $this->_headerText = Mage::helper('onlinedesign')->__('Google Font Manager');
    $this->_addButtonLabel = Mage::helper('onlinedesign')->__('Add Google Font');
    parent::__construct();
  }
}