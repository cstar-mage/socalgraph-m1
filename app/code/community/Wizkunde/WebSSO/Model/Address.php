<?php

/**
 * Class Wizkunde_WebSSO_Model_Address
 */
class Wizkunde_WebSSO_Model_Address
{
    /**
     * @param Varien_Event_Observer $observer
     * @throws Zend_Exception
     */
    public function updateAddress(Varien_Event_Observer $observer)
    {
        $claims = $observer->getData('claims');
        $customer = $observer->getData('customer');

        /**
         * This is called after loggin in with the IDP but before logging into magento
         */
        Mage::dispatchEvent(
            'wizkunde_websso_address_update_before',
            array(
                'claims' => $claims,
                'customer'  => $customer
            )
        );

        $billingAddress = $this->getStreetAddress($claims, 'billing_');

        $fullCustomer = Mage::getModel('customer/customer')->load($customer->getId());

        if($claims->getClaim('billing_street') != '') {
            $addressId = $fullCustomer->getDefaultBilling();

            $customAddress = Mage::getModel('customer/address');

            if ($addressId){
                $customAddress->load($addressId);
            }

            //Build billing and shipping address for customer, for checkout
            $customAddress->addData($billingAddress)
                ->setCustomerId($customer->getId())
                ->setIsDefaultBilling('1')
                ->setSaveInAddressBook(($addressId) ? '0' : '1');
            try {
                $customAddress->save();
            } catch (Exception $ex) {
                Zend_Debug::dump($ex->getMessage());
            }
        }

        $shippingAddress = $this->getStreetAddress($claims, 'shipping_');

        if($claims->getClaim('shipping_street') != '') {
            $addressId = $fullCustomer->getDefaultShipping();
            $customAddress = Mage::getModel('customer/address');

            if ($addressId){
                $customAddress->load($addressId);
            }

            $customAddress->addData($shippingAddress)
                ->setCustomerId($customer->getId())
                ->setIsDefaultShipping('1')
                ->setSaveInAddressBook(($addressId) ? '0' : '1');
            try {
                $customAddress->save();
            } catch (Exception $ex) {
                Zend_Debug::dump($ex->getMessage());
            }
        }

        /**
         * This is called after loggin in with the IDP but before logging into magento
         */
        Mage::dispatchEvent(
            'wizkunde_websso_update_address_after',
            array(
                'claims' => $claims,
                'customer'  => $customer
            )
        );
    }

    /**
     * Generate the existing soapUserData array
     *
     * @param $oUserData
     */
    protected function getStreetAddress($claims, $prefix)
    {
        $address = array();
        $addressClaims = array('street', 'city','region','postcode','country_id','telephone', 'ean', 'company');

        foreach($addressClaims as $key) {
            if($key == 'street') {
                $address['street'] = array('0' => $claims->getClaim($prefix . $key), '1' => '');
            } else {
                $address[$key] = $claims->getClaim($prefix . $key);
            }
        }

        $address['firstname'] = $claims->getClaim('firstname');
        $address['lastname'] = $claims->getClaim('lastname');

        return $address;
    }
}
