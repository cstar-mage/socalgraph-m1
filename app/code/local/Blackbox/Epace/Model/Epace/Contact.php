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
    /**
     * @var Blackbox_Epace_Model_Epace_Customer
     */
    protected $customer = null;
    /**
     * @var Blackbox_Epace_Model_Epace_SalesPerson
     */
    protected $salesPerson = null;

    /**
     * @var Blackbox_Epace_Model_Epace_Country
     */
    protected $country = null;

    protected function _construct()
    {
        $this->_init('Contact', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Customer|bool
     */
    public function getCustomer()
    {
        if (is_null($this->customer)) {
            $this->customer = false;
            if ($this->getData('customer')) {
                $customer = Mage::helper('epace/object')->load('efi/customer', $this->getData('customer'));
                if ($customer->getId()) {
                    $this->customer = $customer;
                }
            }
        }

        return $this->customer;
    }

    public function setCustomer(Blackbox_Epace_Model_Epace_Customer $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_SalesPerson|bool
     */
    public function getSalesPerson()
    {
        if (is_null($this->salesPerson)) {
            $this->salesPerson = false;
            if ($this->getData('salesPerson')) {
                $salesPerson = Mage::helper('epace/object')->load('efi/salesPerson', $this->getData('salesPerson'));
                if ($salesPerson->getId()) {
                    $this->salesPerson = $salesPerson;
                }
            }
        }

        return $this->salesPerson;
    }

    /**
     * @param Blackbox_Epace_Model_Epace_SalesPerson $salesPerson
     * @return $this
     */
    public function setSalesPerson(Blackbox_Epace_Model_Epace_SalesPerson $salesPerson)
    {
        $this->salesPerson = $salesPerson;

        return $this;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Country|bool
     */
    public function getCountry()
    {
        if (is_null($this->country)) {
            $this->country = false;
            if ($this->getData('country')) {
                $country = Mage::helper('epace/object')->load('efi/country', $this->getData('country'));
                if ($country->getId()) {
                    $this->country = $country;
                }
            }
        }

        return $this->country;
    }

    public function setCountry(Blackbox_Epace_Model_Epace_Country $country)
    {
        $this->country = $country;

        return $this;
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