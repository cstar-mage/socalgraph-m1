<?php

class Blackbox_Checkout_Model_Sales_Quote extends Mage_Sales_Model_Quote
{
    /**
     * Assign customer model to quote with billing and shipping address change
     *
     * @param  Mage_Customer_Model_Customer    $customer
     * @param  Mage_Sales_Model_Quote_Address  $billingAddress
     * @param  Mage_Sales_Model_Quote_Address  $shippingAddress
     * @return Mage_Sales_Model_Quote
     */
    public function assignCustomerWithAddressChange(
        Mage_Customer_Model_Customer    $customer,
        Mage_Sales_Model_Quote_Address  $billingAddress  = null,
        Mage_Sales_Model_Quote_Address  $shippingAddress = null
    )
    {
        if ($customer->getId()) {
            $this->setCustomer($customer);

            if (!is_null($billingAddress)) {
                $this->setBillingAddress($billingAddress);
            } else {
                if (!$this->getBillingAddress()->getStorelocatorId() && !$this->getBillingAddress()->getCustomerAddressId()) {
                    $this->_clearAddress($this->getBillingAddress());
                    if (($defaultBillingAddress = $customer->getDefaultBillingAddress()) && $defaultBillingAddress->getId()) {
                        $this->getBillingAddress()->importCustomerAddress($defaultBillingAddress);
                    } else if ($customer->getDefaultBillingStore()) {
                        Mage::helper('blackbox_checkout')->importStorelocatorToAddress($this->getBillingAddress(), $customer->getDefaultBillingStore());
                    }
                }
            }

            foreach ($this->getAllShippingAddresses() as $address) {
                if ($address != $this->getShippingAddress() && !$address->getStorelocatorId() && !$address->getCustomerAddressId()) {
                    $address->isDeleted(true);
                }
            }

            if (!is_null($shippingAddress)) {
                $this->setShippingAddresses(array($shippingAddress));
            } else {
                $firstShippingAddress = $this->getShippingAddress(false);
                if (!$firstShippingAddress->getStorelocatorId() && !$firstShippingAddress->getCustomerAddressId()) {
                    $this->_clearAddress($firstShippingAddress);
                    if (($defaultShippingAddress = $customer->getDefaultShippingAddress()) && $defaultShippingAddress->getId()) {
                        $firstShippingAddress->importCustomerAddress($defaultShippingAddress);
                    } else if ($customer->getDefaultShippingStore()) {
                        Mage::helper('blackbox_checkout')->importStorelocatorToAddress($firstShippingAddress, $customer->getDefaultShippingStore());
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address[] $addresses
     * @return Mage_Sales_Model_Quote
     */
    public function setShippingAddresses(array $addresses)
    {
        $this->removeAddresses(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING);
        foreach ($addresses as $address) {
            $this->addShippingAddress($address, true);
        }
        return $this;
    }

    public function addShippingAddress(Mage_Sales_Model_Quote_Address $address, $add = false)
    {
        if ($add) {
            $this->addAddress($address->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING));
        } else {
            return parent::setShippingAddress($address);
        }
        return $this;
    }

    public function removeAddresses($type)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType()==$type) {
                $address->isDeleted(true);
            }
        }
        return $this;
    }

    protected function _getAddressByType($type, $ignoreEmptyShipping = false)
    {
        if (!$ignoreEmptyShipping) {
            return parent::_getAddressByType($type);
        }

        $firstIgnored = null;
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType() == $type && !$address->isDeleted()) {
                if (!$ignoreEmptyShipping || $type != Mage_Sales_Model_Quote_Address::TYPE_SHIPPING || $address->hasItems()) {
                    return $address;
                } else if (is_null($firstIgnored)) {
                    $firstIgnored = $address;
                }
            }
        }

        if (!is_null($firstIgnored)) {
            return $firstIgnored; // to prevent creating new empty addresses
        }

        $address = Mage::getModel('sales/quote_address')->setAddressType($type);
        $this->addAddress($address);
        return $address;
    }

    public function getShippingAddress($ignoreEmptyShipping = true)
    {
        if (Mage::helper('blackbox_checkout')->getShippingAddressPaymentMethodMode()) {
            return $this->_getRichestShippingAddress();
        }
        return $this->_getAddressByType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING, $ignoreEmptyShipping);
    }

    /**
     * Ignore shipping addresses without items
     *
     * @return array
     */
    public function getAllAddresses()
    {
        $addresses = array();
        foreach ($this->getAddressesCollection() as $address) {
            if (!$address->isDeleted() && !($address->getType() == Mage_Sales_Model_Quote_Address::TYPE_SHIPPING && !$address->hasItems())) {
                $addresses[] = $address;
            }
        }
        return $addresses;
    }

    public function collectQuoteItemsData()
    {
        if (count($this->getAllShippingAddresses()) <= 1) {
            return $this;
        }
        $fields = array(
            'qty',
            'discount_amount',
            'tax_amount',
            'row_total',
            'base_row_total',
            'row_total_with_discount',
            'base_discount_amount',
            'base_tax_amount',
            'row_weight',
            'row_total_incl_tax',
            'base_row_total_incl_tax',
            'hidden_tax_amount',
            'base_hidden_tax_amount',
        );
        $data = array();
        /** @var Mage_Sales_Model_Quote_Address $address */
        foreach($this->getAllShippingAddresses() as $address) {
            /** @var Mage_Sales_Model_Quote_Address_Item $addressItem */
            foreach ($address->getAllItems() as $addressItem) {
                foreach ($fields as $field) {
                    $data[$addressItem->getQuoteItemId()][$field] += (float)$addressItem->getData($field);
                }
            }
        }
        foreach ($this->getAllVisibleItems() as $item) {
            foreach ($fields as $field) {
                $item->setData($field, $data[$item->getId()][$field] ?: 0);
            }
        }

        return $this;
    }

    public function unsetItemsCollection()
    {
        $this->_items = null;
    }

    protected function _getRichestShippingAddress()
    {
        $richest = null;
        $firstIgnored = null;
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType() == Mage_Sales_Model_Quote_Address::TYPE_SHIPPING && !$address->isDeleted()) {
                if (is_null($richest) || $richest->getBaseShippingAmount() < $address->getBaseShippingAmount()) {
                    $richest = $address;
                }
            }
        }

        if (!is_null($richest)) {
            return $richest;
        }

        $address = Mage::getModel('sales/quote_address')->setAddressType($type);
        $this->addAddress($address);
        return $address;
    }

    protected function _clearAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $address->setData([
            'address_id' => $address->getId(),
            'quote_id' => $address->getQuoteId(),
            'created_at' => $address->getCreatedAt(),
            'updated_at' => $address->getUpdatedAt(),
            'address_type' => $address->getAddressType()
        ]);
    }
}