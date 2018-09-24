<?php

class Blackbox_Checkout_Block_Checkout_Onepage_Shipping_Method_Available
    extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
    public function setAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $this->_address = $address;
        $this->_rates = null;
    }
}