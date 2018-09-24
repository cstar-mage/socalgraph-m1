<?php
class Cmsmart_Onlinedesign_Block_Adminhtml_Catart extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_catart';
    $this->_blockGroup = 'onlinedesign';
    $this->_headerText = Mage::helper('onlinedesign')->__('Categories Art Manager');
    $this->_addButtonLabel = Mage::helper('onlinedesign')->__('Add Category');
    parent::__construct();
  }
}