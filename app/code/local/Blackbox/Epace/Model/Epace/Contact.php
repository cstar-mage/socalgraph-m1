<?php

/**
 * @method string getLookupHint()
 * @method string getFirstName()
 * @method string getLastName()
 * @method string getMobilePhoneNumber()
 * @method string getBusinessPhoneNumber()
 * @method string getBusinessPhoneExtension()
 * @method string getEmail()
 * @method string getCompanyName()
 * @method string getAddress1()
 * @method string getCity()
 * @method string getState()
 * @method int getZip()
 * @method int getTaxableCode()
 * @method string getSalesTax()
 * @method int getShipVia()
 * @method int getTerms()
 * @method bool getResidential()
 * @method bool getActive()
 * @method bool getProspect()
 * @method bool getDoNotCall()
 * @method bool getDoNotEmail()
 * @method bool getNeedsInfo()
 * @method bool getCrm()
 * @method string getDefaultCurrency()
 * @method bool getAutoUpdate()
 * @method bool getJobContact()
 * @method bool getFailedGPSLookup()
 * @method bool getMetroAreaForced()
 * @method bool getUseAlternateText()
 * @method bool getDsfShipTo()
 * @method bool getDsfUser()
 * @method bool getAltAutoUpdate()
 * @method bool getAltBill()
 * @method bool getBillTo()
 * @method bool getShipTo()
 * @method bool getGlobalContact()
 * @method string getStateKey()
 *
 * Class Blackbox_Epace_Model_Epace_Contact
 */
class Blackbox_Epace_Model_Epace_Contact extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('Contact', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Customer|bool
     */
    public function getCustomer()
    {
        return $this->_getObject('customer', 'customer', 'efi/customer', true);
    }

    public function setCustomer(Blackbox_Epace_Model_Epace_Customer $customer)
    {
        return $this->_setObject('customer', $customer);
    }

    /**
     * @return Blackbox_Epace_Model_Epace_SalesPerson|bool
     */
    public function getSalesPerson()
    {
        return $this->_getObject('salesPerson', 'salesPerson', 'efi/salesPerson', true);
    }

    /**
     * @param Blackbox_Epace_Model_Epace_SalesPerson $salesPerson
     * @return $this
     */
    public function setSalesPerson(Blackbox_Epace_Model_Epace_SalesPerson $salesPerson)
    {
        return $this->_setObject('salesPerson', $salesPerson);
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Country|bool
     */
    public function getCountry()
    {
        return $this->_getObject('country', 'country', 'efi/country', true);
    }

    public function setCountry(Blackbox_Epace_Model_Epace_Country $country)
    {
        return $this->_setObject('country', $country);
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'firstName' => '',
            'lastName' => '',
            'email' => '',
            'mobilePhoneNumber' => '',
            'businessPhoneNumber' => '',
            'businessPhoneExtension' => '',
            'companyName' => '',
            'address1' => '',
            'address2' => '',
            'city' => 'string',
            'state' => '',
            'zip' => '',
            'country' => 'int',
            'customer' => 'string',
            'residential' => 'bool',
            'active' => 'bool',
            'prospect' => 'bool',
            'doNotCall' => 'bool',
            'doNotEmail' => 'bool',
            'needsInfo' => 'bool',
            'crm' => 'bool',
            'defaultCurrency' => '',
            'autoUpdate' => 'bool',
            'jobContact' => 'bool',
            'failedGPSLookup' => 'bool',
            'metroAreaForced' => 'bool',
            'useAlternateText' => 'bool',
            'imUserName' => '',
            'dsfShipTo' => 'bool',
            'dsfUser' => 'bool',
            'altAutoUpdate' => 'bool',
            'altBill' => 'bool',
            'billTo' => 'bool',
            'shipTo' => 'bool',
            'globalContact' => 'bool',
            'stateKey' => 'string',
        ];
    }
}