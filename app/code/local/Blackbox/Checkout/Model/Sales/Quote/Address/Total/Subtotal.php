<?php

class Blackbox_Checkout_Model_Sales_Quote_Address_Total_Subtotal extends Mage_Sales_Model_Quote_Address_Total_Subtotal
{
    public function _removeItem($address, $item)
    {
        return $this;
    }
}