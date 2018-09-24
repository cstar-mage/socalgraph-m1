<?php

class Blackbox_OrderApproval_Block_Customer_Disapproval_Order_Info_Buttons
    extends Mage_Sales_Block_Order_Info_Buttons
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('blackbox/orderapproval/customer/disapproval/order/info/buttons.phtml');
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getDisapproval()->getOrder();
    }

    /**
     * @return Blackbox_OrderApproval_Model_Disapproval
     */
    public function getDisapproval()
    {
        return Mage::registry('current_disapproval');
    }
}