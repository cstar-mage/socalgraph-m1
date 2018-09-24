<?php

class Blackbox_CustomerStores_Block_Customer_Address_Book extends Mage_Customer_Block_Address_Book
{
    public function getDefaultBillingAddress()
    {
        $customer = $this->getCustomer();
        if ($customer->getDefaultBilling()) {
            return $customer->getDefaultBillingAddress();
        } else if ($customer->getDefaultBillingStore()) {
            return $this->getStorelocator($customer->getDefaultBillingStore());
        }
        return false;
    }

    public function getDefaultShippingAddress()
    {
        $customer = $this->getCustomer();
        if ($customer->getDefaultShipping()) {
            return $customer->getDefaultShippingAddress();
        } else if ($customer->getDefaultShippingStore()) {
            return $this->getStorelocator($customer->getDefaultShippingStore());
        }
        return false;
    }

    public function getStorelocator($id)
    {
        return Mage::getModel('storelocator/storelocator')->load($id);
    }

    public function getAddressHtml($address)
    {
        return Mage::helper('customer_stores')->formatAddressItem($address);
    }

    public function getChangeDefaultAddressUrl($type)
    {
        return $this->getUrl('customer/address/changeDefaultStore', array('type' => $type));
    }

    /**
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }
}