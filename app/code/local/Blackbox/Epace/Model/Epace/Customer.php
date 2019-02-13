<?php

/**
 * @method string getCustName()
 * @method float getAccountBalance()
 * @method string getCustomerStatus()
 * @method string getAddress1()
 * @method float getAging1()
 * @method float getAging2()
 * @method float getAging3()
 * @method string getAging4()
 * @method float getAgingCurrent()
 * @method float getAgingServiceCharge1()
 * @method float getAgingServiceCharge2()
 * @method float getAgingServiceCharge3()
 * @method float getAgingServiceCharge4()
 * @method float getAgingServiceChargeCurrent()
 * @method float getAvgPaymentDays()
 * @method string getCity()
 * @method int getCustomerType()
 * @method float getCreditLimit()
 * @method string getDateHighBalance()
 * @method string getDateLastInvoice()
 * @method string getDateSetup()
 * @method int getDefaultDaysUntilJobDue()
 * @method float getHighestBalance()
 * @method bool getOrderAlert()
 * @method int getPhoneNumber()
 * @method int getSalesCategory()
 * @method string getSalesTax()
 * @method float getSalesYTD()
 * @method int getShipToContact()
 * @method int getShipVia()
 * @method string getState()
 * @method int getStatementCycle()
 * @method int getTaxableCode()
 * @method int getTerms()
 * @method float getWipBalance()
 * @method int getZip()
 * @method bool getCreditCardProcessingEnabled()
 * @method string getShipToFormat()
 * @method string getNextServiceChargeDate()
 * @method bool getApplyDiscountToInvoice()
 * @method bool getCalculateTax()
 * @method bool getCalculateFreight()
 * @method bool getDisplayPrice()
 * @method int getDefaultQuoteLetterType()
 * @method int getShipInNameOf()
 * @method string getDefaultCurrency()
 * @method bool getAllowFailedFreightCheckout()
 * @method int getPlantManagerId()
 * @method bool getDsfShared()
 * @method bool getRequireBillOfLadingPerJob()
 * @method bool getDsfCustomer()
 * @method bool getUseAlternateText()
 * @method bool getAutoAddContact()
 * @method bool getPrintStreamShared()
 * @method bool getPrintStreamCustomer()
 * @method bool getBillToAlt()
 * @method bool getShipBillToAlt()
 * @method bool getDefaultAlt()
 * @method bool getShipToAlt()
 * @method string getInvoiceDeliveryMethod()
 * @method string getStatementDeliveryMethod()
 * @method bool getSageAccountingEnabled()
 * @method bool getJeevesAccountingEnabled()
 * @method float getAgingTotal()
 * @method float getAging1Percent()
 * @method float getAging2Percent()
 * @method float getAging3Percent()
 * @method float getAging4Percent()
 * @method float getCustomerTypeAgingTotalPercent()
 * @method int getUnpostedPaymentBalance()
 * @method float getProbability()
 * @method string getStateKey()
 *
 * Class Blackbox_Epace_Model_Epace_Customer
 */
class Blackbox_Epace_Model_Epace_Customer extends Blackbox_Epace_Model_Epace_AbstractObject
{
    /**
     * @var Blackbox_Epace_Model_Epace_SalesPerson
     */
    protected $salesPerson = null;

    /**
     * @var Blackbox_Epace_Model_Epace_CSR
     */
    protected $csr = null;

    /**
     * @var Blackbox_Epace_Model_Epace_Country
     */
    protected $country = null;

    protected function _construct()
    {
        $this->_init('Customer', 'id');
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
        $this->salesPerson = $salesPerson;

        return $this;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_CSR|bool
     */
    public function getCSR()
    {
        return $this->_getObject('csr', 'csr', 'efi/cSR', true);
    }

    /**
     * @param Blackbox_Epace_Model_Epace_CSR $csr
     * @return $this
     */
    public function setCSR(Blackbox_Epace_Model_Epace_CSR $csr)
    {
        $this->csr = $csr;

        return $this;
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
        $this->country = $country;

        return $this;
    }

    public function getDefinition()
    {
        return [
            'id' => 'string',
            'custName' => '',
            'accountBalance' => '',
            'customerStatus' => '',
            'address1' => '',
            'address2' => '',
            'aging1' => '',
            'aging2' => '',
            'aging3' => '',
            'aging4' => '',
            'agingCurrent' => '',
            'agingServiceCharge1' => '',
            'agingServiceCharge2' => '',
            'agingServiceCharge3' => '',
            'agingServiceCharge4' => '',
            'agingServiceChargeCurrent' => '',
            'avgPaymentDays' => '',
            'city' => 'string',
            'customerType' => 'int',
            'contactFirstName' => '',
            'contactLastName' => '',
            'country' => 'int',
            'creditLimit' => '',
            'csr' => 'int',
            'dateHighBalance' => '',
            'dateLastInvoice' => '',
            'dateSetup' => '',
            'defaultDaysUntilJobDue' => '',
            'highestBalance' => '',
            'orderAlert' => 'bool',
            'phoneNumber' => '',
            'salesCategory' => '',
            'salesPerson' => '',
            'salesTax' => '',
            'salesYTD' => '',
            'shipVia' => '',
            'state' => '',
            'statementCycle' => '',
            'taxableCode' => '',
            'terms' => '',
            'wipBalance' => '',
            'zip' => '',
            'creditCardProcessingEnabled' => 'bool',
            'shipToFormat' => '',
            'nextServiceChargeDate' => '',
            'applyDiscountToInvoice' => 'bool',
            'calculateTax' => 'bool',
            'calculateFreight' => 'bool',
            'displayPrice' => 'bool',
            'defaultQuoteLetterType' => '',
            'shipInNameOf' => '',
            'defaultJob' => '',
            'defaultCurrency' => '',
            'allowFailedFreightCheckout' => 'bool',
            'plantManagerId' => '',
            'dsfShared' => 'bool',
            'defaultContact' => '',
            'requireBillOfLadingPerJob' => 'bool',
            'dsfCustomer' => 'bool',
            'useAlternateText' => 'bool',
            'autoAddContact' => 'bool',
            'printStreamShared' => 'bool',
            'printStreamCustomer' => 'bool',
            'billToAlt' => 'bool',
            'shipBillToAlt' => 'bool',
            'defaultAlt' => 'bool',
            'shipToAlt' => 'bool',
            'defaultDsfContact' => '',
            'invoiceDeliveryMethod' => '',
            'statementDeliveryMethod' => '',
            'processPrintStreamItems' => '',
            'sageAccountingEnabled' => 'bool',
            'jeevesAccountingEnabled' => 'bool',
            'agingTotal' => '',
            'aging1Percent' => '',
            'aging2Percent' => '',
            'aging3Percent' => '',
            'aging4Percent' => '',
            'customerTypeAgingTotalPercent' => '',
            'unpostedPaymentBalance' => '',
            'probability' => '',
            'stateKey' => 'string',
        ];
    }
}