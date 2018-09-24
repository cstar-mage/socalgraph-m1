<?php

class Blackbox_OrderApproval_Block_Customer_Disapproval_View extends Mage_Core_Block_Template
{
    /**
     * @return Blackbox_OrderApproval_Model_Disapproval
     */
    public function getDisapproval()
    {
        if (!parent::getDisapproval()) {
            $this->setDisapproval(Mage::registry('current_disapproval'));
        }

        return parent::getDisapproval();
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getDisapproval()->getOrder();
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }
}