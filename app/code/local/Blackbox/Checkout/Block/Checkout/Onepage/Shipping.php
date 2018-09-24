<?php

class Blackbox_Checkout_Block_Checkout_Onepage_Shipping extends Mage_Checkout_Block_Onepage_Shipping
{
    public function getAddressesHtmlSelect($type)
    {
        if ($this->isCustomerLoggedIn()) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value' => $address->getId(),
                    'label' => $address->format('oneline')
                );
            }

            $addressId = $this->getAddress()->getCustomerAddressId();
            if (empty($addressId)) {
                if (count($this->getQuote()->getAllShippingAddresses()) <= 1) {
                    if ($type == 'billing') {
                        $address = $this->getCustomer()->getPrimaryBillingAddress();
                    } else {
                        $address = $this->getCustomer()->getPrimaryShippingAddress();
                    }
                    if ($address) {
                        $addressId = $address->getId();
                    }
                } else {
                    $addressId = '';
                }
            }

            $select = $this->getLayout()->createBlock('core/html_select')
                ->setName("{$type}[{$this->getShippingId()}][customer_address_id]")
                ->setId("$type{$this->getShippingId()}-address-select")
                ->setClass('address-select')
                ->setExtraParams('onchange="'.$type.'.newAddress(!this.value, jQuery(this).closest(\'.co-shipping\').data(\'id\'))"')
                ->setValue($addressId)
                ->setOptions($options);

            $select->addOption('', Mage::helper('checkout')->__('New Address'));

            return $select->getHtml();
        }
        return '';
    }

    public function getCountryHtmlSelect($type)
    {
        $countryId = $this->getAddress()->getCountryId();
        if (is_null($countryId)) {
            $countryId = Mage::helper('core')->getDefaultCountry();
        }
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName("{$type}[{$this->getShippingId()}][country_id]")
            ->setId("{$type}{$this->getShippingId()}:country_id")
            ->setTitle(Mage::helper('checkout')->__('Country'))
            ->setClass('validate-select')
            ->setValue($countryId)
            ->setOptions($this->getCountryOptions());
        if ($type === 'shipping') {
            $select->setExtraParams('onchange="if(window.shipping)shipping.setSameAsBilling(false);"');
        }

        return $select->getHtml();
    }

    public function getRegionHtmlSelect($type)
    {
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName("{$type}[{$this->getShippingId()}][region]")
            ->setId("{$type}{$this->getShippingId()}:region")
            ->setTitle(Mage::helper('checkout')->__('State/Province'))
            ->setClass('required-entry validate-state')
            ->setValue($this->getAddress()->getRegionId())
            ->setOptions($this->getRegionCollection()->toOptionArray());

        return $select->getHtml();
    }

    public function getShippingId()
    {
        return (int)parent::getShippingId();
    }

    public function canAddMultipleAddresses()
    {
        return true;
    }

    public function setAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $this->_address = $address;
        return $this;
    }

    public function getInputIdPrefix($type)
    {
        return $type . $this->getShippingId();
    }
}