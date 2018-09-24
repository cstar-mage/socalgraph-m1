<?php

class Blackbox_Checkout_Block_Checkout_Cart_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer
{
    public function getAjaxDeleteUrl()
    {
        if ($this->getItem() instanceof Mage_Sales_Model_Quote_Item) {
            return parent::getAjaxDeleteUrl();
        } else {
            return $this->getUrl(
                'checkout/cart/ajaxDeleteAddressItem',
                array(
                    'id' => $this->getItem()->getId(),
                    Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->helper('core/url')->getEncodedUrl(),
                    '_secure' => $this->_getApp()->getStore()->isCurrentlySecure(),
                )
            );
        }
    }

    public function getAddressSelectHtml()
    {
        $item = $this->getItem();
        if ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
            $addressId = $item->getQuoteAddressId();
            $currentShippingAddress = $item->getQuote()->getAddressById($addressId);
            $qItemId = $item->getQuoteItemId();
        } else {
            $currentShippingAddress = $item->getQuote()->getShippingAddress();
            $addressId = 0;
            $qItemId = $item->getId();
        }
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName("cart[$addressId][$qItemId][address_id]")
            ->setClass('item-address-select')
            ->setValue('')
            ->setExtraParams('style="max-width:300px"')
            ->setOptions($this->getAddressOptions($currentShippingAddress));

        return $select->getHtml();
    }

    protected function getAddressOptions($currentShippingAddress)
    {
        if ($currentShippingAddress) {
            $currentAddressId = $this->getQuoteAddressFrontendId($currentShippingAddress);
        } else {
            $currentAddressId = 0;
        }

        $session = Mage::getSingleton('customer/session');
        /** @var Blackbox_CustomerStores_Model_Resource_Address_Collection $addresses */
        $addresses = Mage::getResourceModel('customer_stores/address_collection');
        $addresses->init($session->getCustomer());

        $this->unsetAddressNameFields($currentShippingAddress);

        $options = [
            '' => $currentAddressId ? $currentShippingAddress->format('oneline') : $this->__('Select address...')
        ];

        foreach ($addresses as $address) {
            if ($currentAddressId != $this->getCustomerAddressFrontendId($address)) {
                $this->unsetAddressNameFields($address);
                $options[$this->getCustomerAddressFrontendId($address)] = $address->format('oneline');
            }
        }

        return $options;
    }

    protected function getQuoteAddressFrontendId(Mage_Sales_Model_Quote_Address $address)
    {
        return $address->getStorelocatorId() ?: -$address->getCustomerAddressId();
    }

    protected function getCustomerAddressFrontendId(Mage_Customer_Model_Address $address)
    {
        return $address->getStorelocatorId() ?: -$address->getId();
    }

    protected function unsetAddressNameFields($address)
    {
        $address->unsetData('prefix')
            ->unsetData('firstname')
            ->unsetData('middlename')
            ->unsetData('lastname')
            ->unsetData('suffix');
    }
}