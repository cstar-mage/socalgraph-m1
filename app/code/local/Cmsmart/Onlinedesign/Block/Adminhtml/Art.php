<?php
class Cmsmart_Onlinedesign_Block_Adminhtml_Art extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_art';
    $this->_blockGroup = 'onlinedesign';
    $this->_headerText = Mage::helper('onlinedesign')->__('Art Manager');
    $this->_addButtonLabel = Mage::helper('onlinedesign')->__('Add Art');
    parent::__construct();
  }
}