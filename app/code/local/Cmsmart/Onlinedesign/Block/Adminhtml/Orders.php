<?php
class Cmsmart_Onlinedesign_Block_Adminhtml_Orders extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_orders';
    $this->_blockGroup = 'onlinedesign';
    $this->_headerText = Mage::helper('onlinedesign')->__('Orders Manager');
    $this->_addButtonLabel = Mage::helper('onlinedesign')->__('Add Item');
    parent::__construct();
	$this->_removeButton('add');
  }
}