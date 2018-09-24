<?php

/**
 * Adminhtml approval create
 *
 * @category   Blackbox
 * @package    Blackbox_OrderApproval
 */

class Blackbox_OrderApproval_Block_Adminhtml_Order_Disapproval_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'adminhtml_order_disapproval';
        $this->_mode = 'create';

        parent::__construct();

        $this->_removeButton('save');
        $this->_removeButton('delete');
    }

    /**
     * Retrieve disapproval model instance
     *
     * @return Blackbox_OrderApproval_Model_Disapproval
     */
    public function getDisapproval()
    {
        return Mage::registry('current_disapproval');
    }

    /**
     * Retrieve text for header
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('order_approval')->__('New Disapproval for Order #%s', $this->getDisapproval()->getOrder()->getRealOrderId());
    }

    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('adminhtml/sales_order/view', array('order_id'=>$this->getDisapproval()->getOrderId()));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('order_id' => $this->getDisapproval()->getOrderId()));
    }
}
