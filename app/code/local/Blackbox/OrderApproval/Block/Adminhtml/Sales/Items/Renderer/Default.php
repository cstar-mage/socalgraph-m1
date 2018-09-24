<?php

class Blackbox_OrderApproval_Block_Adminhtml_Sales_Items_Renderer_Default
    extends Mage_Adminhtml_Block_Sales_Items_Renderer_Default
{
    /**
     * Retrieve available order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($this->getApproval())
        {
            return $this->getApproval()->getOrder();
        }

        return parent::getOrder();
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
}