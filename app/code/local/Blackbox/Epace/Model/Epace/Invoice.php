<?php

/**
 * @method string getJobPart()
 * @method string getInvoiceNum()
 * @method string getInvoiceDate()
 * @method int getInvoiceType()
 * @method int getTaxableCode()
 * @method int getShipVia()
 * @method int getTerms()
 * @method int getSalesPerson()
 * @method int getShipToContact()
 * @method string getPreviousAdminStatus()
 * @method string getPreviousProductionStatus()
 * @method string getContactFirstName()
 * @method string getContactLastName()
 * @method string getShipToFormat()
 * @method float getTaxAmount()
 * @method float getCommissionAmount()
 * @method float getLineItemTotal()
 * @method float getCommissionBase()
 * @method bool getCommissionBaseAdjustmentForced()
 * @method float getCommissionAmountAdjustment()
 * @method float getTaxBase()
 * @method bool getTaxBaseAdjustmentForced()
 * @method float getTaxAmountAdjustment()
 * @method float getTotalCost()
 * @method float getTargetSell()
 * @method int getSalesDistributionMethod()
 * @method bool getDistributeCommission()
 * @method bool getLockTaxAmount()
 * @method bool getLockCommissionAmount()
 * @method bool getLockCommissionBase()
 * @method bool getLockTaxBase()
 * @method bool getLockCustomerDiscount()
 * @method bool getPostCompleted()
 * @method float getValueAdded()
 * @method bool getValueAddedForced()
 * @method float getValueAddedCost()
 * @method string getAltCurrency()
 * @method float getAltCurrencyRate()
 * @method string getAltCurrencyRateSource()
 * @method int getAltCurrencyRateSourceNote()
 * @method float getPercentWipToRelieve()
 * @method bool getBalanced()
 * @method bool getSendAsPreInvoice()
 * @method float getDiscountBase()
 * @method bool getDiscountBaseForced()
 * @method int getCommissionSalesCategory()
 * @method float getNextLineNum()
 * @method float getCommissionRate()
 * @method bool getExportedTo3rdParty()
 * @method bool getMemoApproved()
 * @method bool getMemoCommitted()
 * @method string getEnteredBy()
 * @method bool getPosting()
 * @method bool getCalculating()
 * @method bool getReview()
 * @method float getDistributionRemaining()
 * @method int getCloseJob()
 * @method bool getExcludeFromConsolidation()
 * @method string getDateSetup()
 * @method string getTimeSetup()
 * @method int getManufacturingLocation()
 * @method int getTaxDistributionMethod()
 * @method int getTaxDistributionSource()
 * @method int getCommissionDistributionMethod()
 * @method int getCommissionDistributionSource()
 * @method int getSalesTaxBasis()
 * @method bool getPosted()
 * @method float getInvoiceAmount()
 * @method float getInvoiceAmountAdjustment()
 * @method float getMemoAdjustment()
 * @method float getAdjustedInvoiceAmount()
 * @method float getBalanceAmount()
 * @method float getFreightAmount()
 * @method float getTotalExtras()
 * @method float getDepositAmount()
 * @method float getQuantityShipped()
 * @method float getQuantityOrdered()
 * @method bool getUseVAT()
 * @method bool getDistributeTax()
 * @method bool getQuickInvoice()
 * @method bool getShowQuickInvoiceReport()
 * @method bool getTaxingRequired()
 *
 * Class Blackbox_Epace_Model_Epace_Invoice
 */
class Blackbox_Epace_Model_Epace_Invoice extends Blackbox_Epace_Model_Epace_Job_Part_AbstractChild
{
    protected function _construct()
    {
        $this->_init('Invoice', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Invoice_Batch|bool
     */
    public function getBatch()
    {
        return $this->_getObject('batch', 'invoiceBatch', 'efi/invoice_batch');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Invoice_Batch $batch
     * @return $this
     */
    public function setBatch(Blackbox_Epace_Model_Epace_Invoice_Batch $batch)
    {
        return $this->_setObject('batch', $batch);
    }

    /**
     * @return Blackbox_Epace_Model_Epace_SalesCategory|false
     */
    public function getSalesCategory()
    {
        return $this->_getObject('salesCategory', 'salesCategory', 'efi/salesCategory', true);
    }

    /**
     * @param Blackbox_Epace_Model_Epace_SalesCategory $salesCategory
     * @return $this
     */
    public function setSalesCategory(Blackbox_Epace_Model_Epace_SalesCategory $salesCategory)
    {
        return $this->_setObject('salesCategory', $salesCategory);
    }

    /**
     * @return string
     */
    public function getSalesTaxCode()
    {
        return $this->getData('salesTax');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_SalesTax
     */
    public function getSalesTax()
    {
        return $this->_getObject('salesTax', 'salesTax', 'efi/salesTax', true);
    }

    /**
     * @param Blackbox_Epace_Model_Epace_SalesTax $salesTax
     * @return $this
     */
    public function setSalesTax(Blackbox_Epace_Model_Epace_SalesTax $salesTax)
    {
        return $this->_setObject('salesTax', $salesTax);
    }

    /**
     * @return int
     */
    public function getReceivableId()
    {
        return $this->getData('receivable');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Receivable
     */
    public function getReceivable()
    {
        return $this->_getObject('receivable', 'receivable', 'efi/receivable', false, function (Blackbox_Epace_Model_Epace_Receivable $receivable) {
            $receivable->setInvoice($this);
        });
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Receivable $receivable
     * @return $this
     */
    public function setReceivable(Blackbox_Epace_Model_Epace_Receivable $receivable)
    {
        return $this->_setObject('receivable', $receivable);
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Invoice_CommDist[]
     */
    public function getCommDists()
    {
        return $this->_getInvoiceChildren('efi/invoice_commDist_collection');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Invoice_Extra[]
     */
    public function getExtras()
    {
        return $this->_getInvoiceChildren('efi/invoice_extra_collection');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Invoice_Line[]
     */
    public function getLines()
    {
        return $this->_getInvoiceChildren('efi/invoice_line_collection');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Invoice_SalesDist[]
     */
    public function getSalesDists()
    {
        return $this->_getInvoiceChildren('efi/invoice_salesDist_collection');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Invoice_TaxDist[]
     */
    public function getTaxDists()
    {
        return $this->_getInvoiceChildren('efi/invoice_taxDist_collection');
    }

    public function getDefinition()
    {
        return [
            'partiallyBill' => 'bool',
            'id' => 'int',
            'invoiceBatch' => 'int',
            'job' => 'string',
            'jobPart' => 'string',
            'invoiceNum' => 'string',
            'invoiceDate' => 'date',
            'invoiceType' => '',
            'salesCategory' => 'int',
            'salesTax' => 'string',
            'taxableCode' => '',
            'shipVia' => 'int',
            'terms' => '',
            'salesPerson' => 'int',
            'shipToContact' => 'int',
            'previousAdminStatus' => '',
            'previousProductionStatus' => '',
            'contactFirstName' => '',
            'contactLastName' => '',
            'shipToFormat' => '',
            'taxAmount' => '',
            'commissionAmount' => '',
            'lineItemTotal' => '',
            'commissionBase' => '',
            'commissionBaseAdjustmentForced' => 'bool',
            'commissionAmountAdjustment' => '',
            'taxBase' => '',
            'taxBaseAdjustmentForced' => 'bool',
            'taxAmountAdjustment' => '',
            'totalCost' => '',
            'targetSell' => '',
            'salesDistributionMethod' => '',
            'distributeCommission' => 'bool',
            'lockTaxAmount' => 'bool',
            'lockCommissionAmount' => 'bool',
            'lockCommissionBase' => 'bool',
            'lockTaxBase' => 'bool',
            'lockCustomerDiscount' => 'bool',
            'postCompleted' => 'bool',
            'valueAdded' => '',
            'valueAddedForced' => 'bool',
            'valueAddedCost' => '',
            'receivable' => 'int',
            'altCurrency' => '',
            'altCurrencyRate' => '',
            'altCurrencyRateSource' => '',
            'altCurrencyRateSourceNote' => 'string',
            'percentWipToRelieve' => '',
            'balanced' => 'bool',
            'sendAsPreInvoice' => 'bool',
            'discountBase' => '',
            'discountBaseForced' => 'bool',
            'commissionSalesCategory' => '',
            'nextLineNum' => '',
            'commissionRate' => '',
            'exportedTo3rdParty' => 'bool',
            'memoApproved' => 'bool',
            'memoCommitted' => 'bool',
            'enteredBy' => '',
            'posting' => 'bool',
            'calculating' => 'bool',
            'review' => 'bool',
            'distributionRemaining' => '',
            'closeJob' => '',
            'excludeFromConsolidation' => 'bool',
            'dateSetup' => 'date',
            'timeSetup' => 'date',
            'manufacturingLocation' => '',
            'taxDistributionMethod' => '',
            'taxDistributionSource' => '',
            'commissionDistributionMethod' => '',
            'commissionDistributionSource' => '',
            'salesTaxBasis' => '',
            'posted' => 'bool',
            'invoiceAmount' => '',
            'invoiceAmountAdjustment' => '',
            'memoAdjustment' => '',
            'adjustedInvoiceAmount' => '',
            'balanceAmount' => '',
            'freightAmount' => '',
            'totalExtras' => '',
            'depositAmount' => '',
            'quantityShipped' => '',
            'quantityOrdered' => '',
            'useVAT' => 'bool',
            'distributeTax' => 'bool',
            'quickInvoice' => 'bool',
            'showQuickInvoiceReport' => 'bool',
            'taxingRequired' => 'bool',
            'JobPartKey' => 'string',
        ];
    }

    protected function _getInvoiceChildren($collectionName)
    {
        return $this->_getChildItems($collectionName, [
            'invoice' => $this->getId()
        ], function ($item) {
            $item->setInvoice($this);
        });
    }
}