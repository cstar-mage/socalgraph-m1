<?php

/**
 * @method int getId()
 * @method string getCompanyName()
 * @method string getContactFirstName()
 * @method string getContactLastName()
 * @method string getAddress1()
 * @method string getAddress2()
 * @method string getCity()
 * @method string getZip()
 * @method string getEmailAddress()
 * @method string getPhoneNumber()
 * @method int getPoNumber()
 * @method int getTerms()
 * @method bool getDiscountCode()
 * @method string getDateEntered()
 * @method float getOrderTotal()
 * @method float getOriginalTotal()
 * @method float getDiscountAmount()
 * @method string getCreatedBy()
 * @method string getRequester()
 * @method int getPurchaseOrderType()
 * @method float getTaxBase1()
 * @method float getTaxBase2()
 * @method float getTaxAmount1()
 * @method float getTaxAmount2()
 * @method string getFreightOnBoard()
 * @method float getAltCurrencyRate()
 * @method string getAltCurrencyRateSource()
 * @method string getAltCurrencyRateSourceNote()
 * @method bool getConvertEnteredValues()
 * @method bool getPrintStreamShared()
 * @method bool getPoShared()
 * @method bool getAuthorizationRequested()
 * @method string getReceiveDate()
 * @method float getTaxedTotal()
 * @method int getTotalLines()
 * @method string getStateKey()
 *
 * Class Blackbox_Epace_Model_Epace_Purchase_Order
 */
class Blackbox_Epace_Model_Epace_Purchase_Order extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('PurchaseOrder', 'id');
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
     * @return Blackbox_Epace_Model_Epace_Country
     */
    public function getCountry()
    {
        return $this->_getObject('country', 'country', 'efi/country', true);
    }

    /**
     * @return string
     */
    public function getVendorId()
    {
        return $this->getData('vendor');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Vendor
     */
    public function getVendor()
    {
        return $this->_getObject('vendor', 'vendor', 'efi/vendor');
    }

    /**
     * @return int
     */
    public function getVendorContactId()
    {
        return $this->getData('vendorContact');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Contact
     */
    public function getVendorContact()
    {
        return $this->_getObject('vendorContact', 'vendorContact', 'efi/contact');
    }

    /**
     * @return int
     */
    public function getShipViaId()
    {
        return $this->getData('shipVia');
    }

    public function getShipVia()
    {
        return $this->_getObject('shipVia', 'shipVia', 'efi/ship_via', true);
    }

    /**
     * @return Blackbox_Epace_Model_Epace_POStatus
     */
    public function getOrderStatus()
    {
        return $this->_getObject('orderStatus', 'orderStatus', 'efi/pOStatus', true);
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
     * @return Blackbox_Epace_Model_Epace_Purchase_Order_Type
     */
    public function getType()
    {
        return $this->_getObject('purchaseOrderType', 'purchaseOrderType', 'efi/purchase_order_type', true);
    }

    /**
     * @return string
     */
    public function getAltCurrencyCode()
    {
        return $this->getData('altCurrency');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Currency
     */
    public function getAltCurrency()
    {
        return $this->_getObject('altCurrency', 'altCurrency', 'efi/currency', true);
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Purchase_Order_Line[]
     */
    public function getLines()
    {
        return $this->_getChildItems('efi/purchase_order_line_collection', [
            'purchaseOrder' => $this->getId()
        ], function(Blackbox_Epace_Model_Epace_Purchase_Order_Line $line) {
            $line->setPurchaseOrder($this);
        });
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'companyName' => 'string',
            'contactFirstName' => 'string',
            'contactLastName' => 'string',
            'address1' => 'string',
            'address2' => 'string',
            'city' => 'string',
            'zip' => 'string',
            'state' => 'string',
            'country' => 'int',
            'emailAddress' => 'string',
            'phoneNumber' => 'string',
            'poNumber' => 'string',
            'vendor' => 'string',
            'vendorContact' => 'int',
            'terms' => 'int',
            'shipVia' => 'int',
            'orderStatus' => 'string',
            'discountCode' => 'bool',
            'dateEntered' => 'date',
            'orderTotal' => 'float',
            'originalTotal' => 'float',
            'discountAmount' => 'float',
            'createdBy' => 'string',
            'requester' => 'string',
            'shipToContact' => 'int',
            'purchaseOrderType' => 'int',
            'taxBase1' => 'float',
            'taxBase2' => 'float',
            'taxAmount1' => 'float',
            'taxAmount2' => 'float',
            'freightOnBoard' => 'string',
            'altCurrency' => 'string',
            'altCurrencyRate' => 'float',
            'altCurrencyRateSource' => 'string',
            'altCurrencyRateSourceNote' => 'string',
            'convertEnteredValues' => 'bool',
            'printStreamShared' => 'bool',
            'poShared' => 'bool',
            'authorizationRequested' => 'bool',
            'receiveDate' => 'date',
            'taxedTotal' => 'float',
            'totalLines' => 'int',
            'stateKey' => 'string',
        ];
    }
}