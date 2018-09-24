<?php

class Blackbox_Checkout_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $associatedOrders = [];
    protected $_shippingAddressPaymentMethodMode = false;

    public function getAssociatedOrders($order)
    {
        if (!isset($this->associatedOrders[$order->getId()])) {
            if ($order->getAssociatedOrders()) {
                foreach (explode(',', $order->getAssociatedOrders()) as $id) {
                    $this->associatedOrders[$order->getId()][] = Mage::getModel('sales/order')->load($id);
                }
            }
        }

        return (array)$this->associatedOrders[$order->getId()];
    }

    public function formatAddressItem($item)
    {
        if ($item instanceof Magestore_Storelocator_Model_Storelocator) {
            return $this->_formatStorelocator($item);
        } else if ($item->getStorelocatorId()) {
            $storelocator = Mage::getModel('storelocator/storelocator')->load($item->getStorelocatorId());
            if ($storelocator->getId()) {
                return $this->_formatStorelocator($storelocator);
            }
        } else if ($item instanceof Mage_Sales_Model_Quote_Address) {
            return $item->format('text');
        }
    }

    /**
     * @param Magestore_Storelocator_Model_Storelocator $storelocator
     * @return string
     */
    public function getStorelocatorAddAllUrl($storelocator = null)
    {
        if ($storelocator) {
            return $this->_getUrl('checkout/cart/addAll', array('_secure' => Mage::app()->getRequest()->isSecure(), 'storelocator_id' => $storelocator->getId()));
        } else {
            return $this->_getUrl('checkout/cart/addAll', array('_secure' => Mage::app()->getRequest()->isSecure()));
        }

    }

    /**
     * @param Magestore_Storelocator_Model_Storelocator $storelocator
     * @return string
     */
    public function getStorelocatorRemoveUrl($storelocator = null)
    {
        if ($storelocator) {
            return $this->_getUrl('checkout/cart/removeStorelocator', array('_secure' => Mage::app()->getRequest()->isSecure(), 'storelocator_id' => $storelocator->getId()));
        } else {
            return $this->_getUrl('checkout/cart/removeStorelocator', array('_secure' => Mage::app()->getRequest()->isSecure()));
        }

    }


    /**
     * @param Magestore_Storelocator_Model_Storelocator $storelocator
     * @return bool
     */
    public function isStorelocatorInCart($storelocator)
    {
        if (!$storelocator->getId()) {
            return false;
        }

        /** @var Mage_Checkout_Model_Cart $cart */
        $cart = Mage::getSingleton('checkout/cart');

        foreach ($cart->getQuote()->getAllShippingAddresses() as $address) {
            if ($address->getStorelocatorId() == $storelocator->getId()) {
                return true;
            }
        }

        return false;
    }

    public function importStorelocatorToAddress($address, $storelocator)
    {
        if (!is_object($storelocator)) {
            $storelocator = Mage::getModel('storelocator/storelocator')->load($storelocator);
        }

        if (!$storelocator->getId()) {
            return;
        }

        $data = array(
            'email' => $storelocator->getEmail(),
            'street' => [
                $storelocator->getAddress()
            ],
            'city' => $storelocator->getCity(),
            'region_id' => $storelocator->getStateId(),
            'region' => $storelocator->getState(),
            'postcode' => $storelocator->getZipcode(),
            'country_id' => $storelocator->getCountry(),
            'telephone' => $storelocator->getPhone(),
            'fax' => $storelocator->getFax(),
        );

        /* @var $addressForm Mage_Customer_Model_Form */
        $addressForm = Mage::getModel('customer/form');
        $addressForm->setFormCode('customer_address_edit')
            ->setEntityType('customer_address')
            ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

        $addressForm->setEntity($address);
        // emulate request object
        $addressData    = $addressForm->extractData($addressForm->prepareRequest($data));
        $addressForm->compactData($addressData);
        //unset billing address attributes which were not shown in form
        foreach ($addressForm->getAttributes() as $attribute) {
            if (!isset($data[$attribute->getAttributeCode()])) {
                $address->setData($attribute->getAttributeCode(), NULL);
            }
        }
        $address->setCustomerAddressId(null);
        // Additional form data, not fetched by extractData (as it fetches only attributes)
        $address->setSaveInAddressBook(0);
        $address->setStorelocatorId($storelocator->getId());
        $address->setStorelocatorName($storelocator->getName());

        $address->setEmail($data['email']);

        // validate billing address
        if (($validateRes = $address->validate()) !== true) {
            return array('error' => 1, 'message' => $validateRes);
        }

        $address->implodeStreetAddress();
    }

    public function processCartByAddressItems($quote)
    {
        return count($quote->getAllShippingAddresses()) > 1 || ($shippingAddress = $quote->getShippingAddress()) && $shippingAddress->getId() && ($shippingAddress->getCustomerAddressId() || $shippingAddress->getStorelocatorId());
    }

    public function setShippingAddressPaymentMethodMode($value)
    {
        $this->_shippingAddressPaymentMethodMode = $value;
    }

    public function getShippingAddressPaymentMethodMode()
    {
        return $this->_shippingAddressPaymentMethodMode;
    }

    protected function _formatStorelocator($item)
    {
        return implode(', ', array_filter([
            $item->getName(),
            $item->getAddress(),
            $item->getCity(),
            $item->getCountry(),
            $item->getZipcode(),
            $item->getState(),
            $item->getEmail(),
            $item->getPhone() ? 'T: ' . $item->getPhone() : ''
        ]));
    }
}