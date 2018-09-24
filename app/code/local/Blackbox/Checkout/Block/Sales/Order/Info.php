<?php

class Blackbox_Checkout_Block_Sales_Order_Info extends Mage_Sales_Block_Order_Info
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('blackbox/sales/order/info.phtml');
    }
}