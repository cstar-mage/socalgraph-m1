<?php

class Blackbox_Epace_Model_Epace_Job_Material extends Blackbox_Epace_Model_Epace_Job_Part_AbstractChild
{
    protected function _construct()
    {
        $this->_init('JobMaterial', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'job' => '',
            'jobPart' => '',
            'stockNumber' => '',
            'description' => '',
            'plannedQuantity' => '',
            'pulledQuantity' => '',
            'paper' => 'bool',
            'uom' => '',
            'unitPrice' => '',
            'fromEstimating' => 'bool',
            'manual' => 'bool',
            'estimateSource' => '',
            'reviewedForPO' => 'bool',
            'purchasedQuantity' => '',
            'vendorPaper' => 'bool',
            'altCurrency' => '',
            'altCurrencyRate' => '',
            'altCurrencyRateSource' => '',
            'altCurrencyRateSourceNote' => '',
            'mWeightForced' => 'bool',
            'ink' => 'bool',
            'cover' => 'bool',
            'roundSheetsToForced' => 'bool',
            'buySize' => '',
            'runSize' => '',
            'ordered' => '',
            'quantityRemaining' => '',
            'JobPartKey' => '',
        ];
    }
}