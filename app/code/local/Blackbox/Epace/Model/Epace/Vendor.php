<?php

/**
 * @method string getId()
 * @method string getName()
 * @method string getAddress1()
 * @method string getCity()
 * @method string getZip()
 * @method string getState()
 * @method int getCountry()
 * @method int getVendorType()
 * @method int getTerms()
 * @method bool getPrint1099()
 * @method string getContactFirstName()
 * @method string getContactLastName()
 * @method float getCurrentBalance()
 * @method string getSetupDate()
 * @method int getShipVia()
 * @method string getEmailAddress()
 * @method string getPhoneNumber()
 * @method int getShipToContact()
 * @method int getShipFromContact()
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
            'manufacturingLocation' => 'int',
            'remittanceDeliveryMethod' => 'string',
            'sageAccountingEnabled' => 'bool',
            'jeevesAccountingEnabled' => 'bool',
            'stateKey' => 'string',
        ];
    }
}