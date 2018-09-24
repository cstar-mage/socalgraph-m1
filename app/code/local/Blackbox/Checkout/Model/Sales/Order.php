<?php

class Blackbox_Checkout_Model_Sales_Order extends Mage_Sales_Model_Order
{
    public function setShippingAddresses(array $addresses)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType()=='shipping' && !$address->isDeleted()) {
                $address->isDeleted(true);
            }
        }

        foreach ($addresses as $address) {
            $this->addAddress($address->setAddressType('shipping'));
        }
    }

    public function getShippingAddresses()
    {
        $result = array();

        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType()=='shipping' && !$address->isDeleted()) {
                $result[] = $address;
            }
        }

        return $result;
    }
}