<?php

/**
 * Adminhtml approval create
 *
 * @category   Blackbox
 * @package    Blackbox_OrderApproval
 */

class Blackbox_OrderApproval_Block_Adminhtml_Order_Approval_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'adminhtml_order_approval';
        $this->_mode = 'create';

        parent::__construct();

        $this->_removeButton('save');
        $this->_removeButton('delete');
    }

    /**
     * Retrieve invoice model instance
     *
     * @return Blackbox_OrderApproval_Model_Approval
     */
    public function getApproval()
    {
        return Mage::registry('current_approval');
    }

    /**
     * Retrieve text for header
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('order_approval')->__('New Approval for Order #%s', $this->getApproval()->getOrder()->getRealOrderId());
    }

    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('adminhtml/sales_order/view', array('order_id'=>$this->getApproval()->getOrderId()));
    }
}
