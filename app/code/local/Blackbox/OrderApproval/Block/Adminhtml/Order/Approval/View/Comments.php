<?php

/**
 * Adminhtml sales approval comment view block
 *
 * @category   Blackbox
 * @package    Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Block_Adminhtml_Order_Approval_View_Comments extends Mage_Adminhtml_Block_Text_List
{
    /**
     * Retrieve approval order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getApproval()->getOrder();
    }

    /**
     * Retrieve source
     *
     * @return Blackbox_OrderApproval_Model_Approval
     */
    public function getSource()
    {
        return $this->getApproval();
    }

    /**
     * Retrieve approval model instance
     *
     * @return Blackbox_OrderApproval_Model_Approval
     */
    public function getApproval()
    {
        return Mage::registry('current_approval');
    }
}
