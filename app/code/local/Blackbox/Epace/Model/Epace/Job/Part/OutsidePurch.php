<?php

class Blackbox_Epace_Model_Epace_Job_Part_OutsidePurch extends Blackbox_Epace_Model_Epace_Job_Part_AbstractChild
{
    protected function _construct()
    {
        $this->_init('JobPartOutsidePurch', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'job' => '',
            'jobPart' => '',
            'quantity' => '',
            'setupCost' => '',
            'totalCost' => '',
            'outsidePurchaseMarkup' => '',
            'outsidePurchaseSetupMarkupForced' => 'bool',
            'quoteNum' => '',
            'description' => '',
            'vendor' => '',
            'used' => 'bool',
            'activityCode' => '',
            'reviewedForPO' => 'bool',
            'manual' => 'bool',
            'estimateSource' => '',
            'purchasedQuantity' => '',
            'altCurrency' => '',
            'altCurrencyRate' => '',
            'altCurrencyRateSource' => '',
            'altCurrencyRateSourceNote' => 'string',
            'unitPrice' => '',
            'uom' => '',
            'mWeight' => '',
            'setupActivityCode' => '',
            'JobPartKey' => '',
        ];
    }
}