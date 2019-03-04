<?php

class Blackbox_Epace_Model_Epace_Invoice_TaxDist extends Blackbox_Epace_Model_Epace_Invoice_ChildAbstract
{
    protected function _construct()
    {
        $this->_init('InvoiceTaxDist', 'id');
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

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'invoice' => 'int',
            'amount' => '',
            'taxBase' => '',
            'memoCreated' => 'bool',
            'salesTax' => '',
            'taxableCode' => '',
            'lockTaxBase' => 'bool',
            'lockAmount' => 'bool',
            'includesHandling' => 'bool',
            'taxFreight' => 'bool',
            'taxFreightAndHandling' => 'bool',
            'taxRate' => '',
            'taxFreightAdjustment' => 'bool',
            'state' => '',
            'zip' => '',
            'country' => '',
            'manual' => 'bool',
            'calculationError' => 'bool',
            'edited' => 'bool',
            'forcedTaxDist' => 'bool',
            'adjustmentTaxDist' => 'bool',
            'posted' => 'bool',
            'distributionRemaining' => '',
            'amountAdjustment' => '',
            'adjustedTotal' => '',
            'adjustedFreight' => '',
            'adjustedAmount' => '',
            'stateKey' => '',
        ];
    }
}