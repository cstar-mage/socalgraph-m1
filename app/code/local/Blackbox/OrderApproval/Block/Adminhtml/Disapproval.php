<?php

/**
 * Adminhtml sales disapprovals block
 *
 * @category   Blackbox
 * @package    Blackbox_OrderApproval
 */

class Blackbox_OrderApproval_Block_Adminhtml_Disapproval extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_disapproval';
        $this->_blockGroup = 'order_approval';
        $this->_headerText = Mage::helper('order_approval')->__('Disapprovals');
        parent::__construct();
        $this->_removeButton('add');
    }
}
