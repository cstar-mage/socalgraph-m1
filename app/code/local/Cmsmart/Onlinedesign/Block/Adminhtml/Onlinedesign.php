<?php
class Cmsmart_Onlinedesign_Block_Adminhtml_Onlinedesign extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_onlinedesign';
    $this->_blockGroup = 'onlinedesign';
    $this->_headerText = Mage::helper('onlinedesign')->__('Product Design Manager');
    $this->_addButtonLabel = Mage::helper('onlinedesign')->__('Add Item');
    parent::__construct();
	$this->_removeButton('add');
  }
}