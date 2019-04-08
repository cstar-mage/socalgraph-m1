<?php

/**
 * @method string getId()
 * @method string getName()
 * @method string getAddress1()
 * @method string getCity()
 * @method string getZip()
 * @method int getVendorType()
 * @method int getTerms()
 * @method bool getPrint1099()
 * @method string getContactFirstName()
 * @method string getContactLastName()
 * @method float getCurrentBalance()
 * @method string getSetupDate()
 * @method string getEmailAddress()
 * @method string getPhoneNumber()
 * @method bool getActive()
 * @method float getYtdPurch()
 * @method float getYtdPayments()
 * @method float getPriorYearPurchaseDollars()
 * @method bool getPoLinesTaxable()
 * @method string getDefaultFreightOnBoard()
 * @method bool getPaperUseLastBracket()
 * @method string getDefaultCurrency()
 * @method float getAgingCurrent()
 * @method float getAging1()
 * @method float getAging2()
 * @method float getAging3()
 * @method float getAging4()
 * @method bool getPaymentAlt()
 * @method bool getShipFromAlt()
 * @method bool getShipToAlt()
 * @method int getManufacturingLocation()
 * @method string getRemittanceDeliveryMethod()
 * @method bool getSageAccountingEnabled()
 * @method bool getJeevesAccountingEnabled()
 * @method string getStateKey()
 *
 * Class Blackbox_Epace_Model_Epace_Vendor
 */
class Blackbox_Epace_Model_Epace_Vendor extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('Vendor', 'id');
    }

    /**
     * @return string
     */
    public function getStateCode()
    {
        return $this->getData('state');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_State
     */
    public function getState()
    {
        return $this->_getObject('state', 'stateKey', 'efi/state', true);
    }

    /**
     * @return int
     */
    public function getShipViaId()
    {
        return $this->getData('shipVia');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Ship_Via
     */
    public function getShipVia()
    {
        return $this->_getObject('shipVia', 'shipVia', 'efi/ship_via', true);
    }

    /**
     * @return int
     */
    public function getShipToContactId()
    {
        return $this->getData('shipToContact');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Contact
     */
    public function getShipToContact()
    {
        return $this->_getObject('shipToContact', 'shipToContact', 'efi/contact');
    }

    /**
     * @return int
     */
    public function getPaymentContactId()
    {
        return $this->getData('paymentContact');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Contact
     */
    public function getPaymentContact()
    {
        return $this->_getObject('paymentContact', 'paymentContact', 'efi/contact');
    }

    /**
     * @return int
     */
    public function getShipFromContactId()
    {
        return $this->getData('shipFromContact');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Contact
     */
    public function getShipFromContact()
    {
        return $this->_getObject('shipFromContact', 'shipFromContact', 'efi/contact');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Country|bool
     */
    public function getCountry()
    {
        return $this->_getObject('country', 'country', 'efi/country', true);
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Salutation
     */
    public function getSalutation()
    {
        return $this->_getObject('salutation', 'salutation', 'efi/salutation');
    }

    public function getDefinition()
    {
        return [
            'id' => 'string',
            'name' => 'string',
            'address1' => 'string',
            'city' => 'string',
            'zip' => 'string',
            'state' => 'string',
            'country' => 'int',
            'vendorType' => 'int',
            'terms' => 'int',
            'print1099' => 'bool',
            'contactFirstName' => 'string',
            'contactLastName' => 'string',
            'currentBalance' => 'float',
            'setupDate' => 'date',
            'shipVia' => 'int',
            'emailAddress' => 'string',
            'phoneNumber' => 'string',
            'shipToContact' => 'int',
            'paymentContact' => 'int',
            'shipFromContact' => 'int',
            'active' => 'bool',
            'ytdPurch' => 'float',
            'ytdPayments' => 'float',
            'priorYearPurchaseDollars' => 'float',
            'poLinesTaxable' => 'bool',
            'defaultFreightOnBoard' => 'string',
            'paperUseLastBracket' => 'bool',
            'defaultCurrency' => 'string',
            'agingCurrent' => 'float',
            'aging1' => 'float',
            'aging2' => 'float',
            'aging3' => 'float',
            'aging4' => 'float',
            'paymentAlt' => 'bool',
            'shipFromAlt' => 'bool',
            'shipToAlt' => 'bool',
            'salutation' => 'int',
            'manufacturingLocation' => 'int',
            'remittanceDeliveryMethod' => 'string',
            'sageAccountingEnabled' => 'bool',
            'jeevesAccountingEnabled' => 'bool',
            'stateKey' => 'string',
        ];
    }
}