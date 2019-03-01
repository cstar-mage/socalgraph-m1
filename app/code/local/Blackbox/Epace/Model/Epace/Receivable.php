<?php

/**
 * @method int getStatus()
 * @method int getInvoiceNumber()
 * @method string getPoNumber()
 * @method int getJobPart()
 * @method string getInvoiceDate()
 * @method string getDueDate()
 * @method string getExpectedPaymentDate()
 * @method float getTaxAmount()
 * @method float getTargetSell()
 * @method int getTaxableCode()
 * @method float getTaxBase()
 * @method float getCommissionAmount()
 * @method float getCommissionBase()
 * @method int getCustomerType()
 * @method int getGlAccountingPeriod()
 * @method string getDescription()
 * @method float getAmountDue()
 * @method float getOriginalAmount()
 * @method float getInvoiceAmount()
 * @method float getFreightAmount()
 * @method string getContactFirstName()
 * @method string getContactLastName()
 * @method string getDiscountDate()
 * @method float getDiscountAvailable()
 * @method int getSalesCategory()
 * @method string getDateComissionPaid()
 * @method string getDatePaidOff()
 * @method int getOrginalBatchId()
 * @method string getAltCurrency()
 * @method float getAltCurrencyRate()
 * @method string getAltCurrencyRateSource()
 * @method int getAltCurrencyRateSourceNote()
 * @method string getGlRegisterNumber()
 * @method float getCommissionRate()
 * @method string getDateSetup()
 * @method string getTimeSetup()
 * @method bool getSendDunningLetter()
 * @method int getAgingCategory()
 * @method float getTaxRate1Amount()
 * @method float getTaxRate2Amount()
 * @method float getTaxRate3Amount()
 * @method float getTaxRate4Amount()
 * @method float getTaxRate5Amount()
 * @method float getTaxRate6Amount()
 * @method float getTaxRate7Amount()
 * @method float getDiscountApplied()
 * @method float getUnpaidAmount()
 * @method int getAvailRemainingDeposit()
 *
 * Class Blackbox_Epace_Model_Epace_Receivable
 */
class Blackbox_Epace_Model_Epace_Receivable extends Blackbox_Epace_Model_Epace_Job_Part_AbstractChild
{
    protected function _construct()
    {
        $this->_init('Receivable', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Customer
     */
    public function getCustomer()
    {
        return $this->_getObject('customer', 'customer', 'efi/customer');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Customer $customer
     * @return $this
     */
    public function setCustomer(Blackbox_Epace_Model_Epace_Customer $customer)
    {
        return $this->_setObject('customer', $customer);
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Invoice|bool
     */
    public function getInvoice()
    {
        if ($this->getAltCurrencyRateSource() == 'Invoice') {
            return $this->_getObject('invoice', 'altCurrencyRateSourceNote', 'efi/invoice');
        } else if ($this->_hasObjectField('invoice')) {
            return $this->_getObjectField('invoice');
        } else {
            return false;
        }
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Invoice $invoice
     * @return $this
     */
    public function setInvoice(Blackbox_Epace_Model_Epace_Invoice $invoice)
    {
        return $this->_setObject('invoice', $invoice);
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Receivable_Line[]
     */
    public function getLines()
    {
        return $this->_getChildItems('efi/receivable_line_collection', [
            'receivable' => $this->getId()
        ], function (Blackbox_Epace_Model_Epace_Receivable_Line $line) {
            $line->setReceivable($this);
        });
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'customer' => 'string',
            'status' => 'int',
            'invoiceNumber' => 'string',
            'job' => 'string',
            'jobPart' => 'string',
            'invoiceDate' => 'date',
            'dueDate' => 'date',
            'expectedPaymentDate' => 'date',
            'taxAmount' => '',
            'targetSell' => '',
            'taxableCode' => '',
            'taxBase' => '',
            'commissionAmount' => '',
            'commissionBase' => '',
            'customerType' => '',
            'glAccountingPeriod' => '',
            'description' => '',
            'amountDue' => '',
            'originalAmount' => '',
            'invoiceAmount' => '',
            'freightAmount' => '',
            'discountDate' => 'date',
            'discountAvailable' => '',
            'salesCategory' => '',
            'dateCommissionPaid' => 'date',
            'datePaidOff' => 'date',
            'orginalBatchId' => '',
            'altCurrency' => '',
            'altCurrencyRate' => 'float',
            'altCurrencyRateSource' => 'string',
            'altCurrencyRateSourceNote' => 'string',
            'glRegisterNumber' => 'string',
            'commissionRate' => 'float',
            'sendDunningLetter' => 'bool',
            'dateSetup' => 'date',
            'timeSetup' => 'date',
            'sendDunningLetter' => 'bool',
            'agingCategory' => '',
            'taxRate1Amount' => 'float',
            'taxRate2Amount' => 'float',
            'taxRate3Amount' => 'float',
            'taxRate4Amount' => 'float',
            'taxRate5Amount' => 'float',
            'taxRate6Amount' => 'float',
            'taxRate7Amount' => 'float',
            'discountApplied' => 'float',
            'unpaidAmount' => 'float',
            'availRemainingDeposit' => 'float',
        ];
    }
}