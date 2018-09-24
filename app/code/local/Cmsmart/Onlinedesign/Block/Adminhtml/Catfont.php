<?php
class Cmsmart_Onlinedesign_Block_Adminhtml_Catfont extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_catfont';
    $this->_blockGroup = 'onlinedesign';
    $this->_headerText = Mage::helper('onlinedesign')->__('Categories Font Manager');
    $this->_addButtonLabel = Mage::helper('onlinedesign')->__('Add Category');
    parent::__construct();
  }
}