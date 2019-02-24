<?php

class Blackbox_Epace_Model_Epace_Invoice_TaxDist extends Blackbox_Epace_Model_Epace_Invoice_ChildAbstract
{
    protected function _construct()
    {
        $this->_init('InvoiceTaxDist', 'id');
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