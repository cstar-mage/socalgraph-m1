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
    /**
     * @var Blackbox_Epace_Model_Epace_Invoice
     */
    protected $invoice;

    protected function _construct()
    {
        $this->_init('Receivable', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Invoice|bool
     */
    public function getInvoice()
    {
        if (is_null($this->invoice)) {
            $this->invoice = false;

            if (!empty($this->getInvoiceNumber())) {
                /** @var Blackbox_Epace_Model_Resource_Epace_Invoice_Collection $collection */
                $collection = $this->_getCollection('efi/invoice');
                $collection->addFilter('invoiceNum', $this->getInvoiceNumber());
                $invoice = $collection->getFirstItem();
                if ($invoice->getId()) {
                    $this->invoice = $invoice;
                }
            }
        }
        return $this->invoice;
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Invoice $invoice
     * @return $this
     */
    public function setInvoice(Blackbox_Epace_Model_Epace_Invoice $invoice)
    {
        $this->invoice = $invoice;

        return $this;
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
            'invoiceDate' => '',
            'dueDate' => '',
            'expectedPaymentDate' => '',
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
            'discountDate' => '',
            'discountAvailable' => '',
            'salesCategory' => '',
            'dateCommissionPaid' => '',
            'orginalBatchId' => '',
            'altCurrency' => '',
            'altCurrencyRate' => '',
            'altCurrencyRateSource' => '',
            'altCurrencyRateSourceNote' => '',
            'glRegisterNumber' => '',
            'commissionRate' => '',
            'sendDunningLetter' => 'bool',
            'agingCategory' => '',
            'taxRate1Amount' => '',
            'taxRate2Amount' => '',
            'taxRate3Amount' => '',
            'taxRate4Amount' => '',
            'taxRate5Amount' => '',
            'taxRate6Amount' => '',
            'taxRate7Amount' => '',
            'discountApplied' => '',
            'unpaidAmount' => '',
            'availRemainingDeposit' => '',
        ];
    }
}