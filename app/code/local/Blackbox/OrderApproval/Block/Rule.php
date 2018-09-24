<?php

/**
 * Order Approval rules
 *
 * @package    Blackbox_OrderApproval
 */

class Blackbox_OrderApproval_Block_Rule extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'rule';
        $this->_blockGroup = 'order_approval';
        $this->_headerText = Mage::helper('order_approval')->__('Order Approval Rules');
        $this->_addButtonLabel = Mage::helper('order_approval')->__('Add New Rule');

        parent::__construct();
    }
}
