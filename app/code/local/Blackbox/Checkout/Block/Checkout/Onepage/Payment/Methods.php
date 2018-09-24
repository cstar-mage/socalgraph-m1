<?php

class Blackbox_Checkout_Block_Checkout_Onepage_Payment_Methods extends Mage_Checkout_Block_Onepage_Payment_Methods
{
    public function getMethods()
    {
        try {
            Mage::helper('blackbox_checkout')->setShippingAddressPaymentMethodMode(true);
            return parent::getMethods();
        } finally {
            Mage::helper('blackbox_checkout')->setShippingAddressPaymentMethodMode(false);
        }
    }
}