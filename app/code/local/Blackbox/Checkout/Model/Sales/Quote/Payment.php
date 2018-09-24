<?php

class Blackbox_Checkout_Model_Sales_Quote_Payment extends Mage_Sales_Model_Quote_Payment
{
    public function importData(array $data)
    {
        try {
            Mage::helper('blackbox_checkout')->setShippingAddressPaymentMethodMode(true);
            return parent::importData($data);
        } finally {
            Mage::helper('blackbox_checkout')->setShippingAddressPaymentMethodMode(false);
        }
    }
}