<?php

class Blackbox_LoginHistory_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getCustomerCountryOptions($customerId)
    {
        $result = [];
        foreach ($this->_getResource()->getCustomerCountries($customerId) as $country) {
            $result[$country] = $country;
        }
        return $result;
    }

    public function getCustomerCityOptions($customerId)
    {
        $result = [];
        foreach ($this->_getResource()->getCustomerCities($customerId) as $city) {
            $result[$city] = $city;
        }
        return $result;
    }

    /**
     * @return Blackbox_LoginHistory_Model_Resource_Login
     */
    protected function _getResource()
    {
        return Mage::getResourceSingleton('login_history/login');
    }
}