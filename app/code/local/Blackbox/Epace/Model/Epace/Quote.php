<?php

class Blackbox_Epace_Model_Epace_Quote extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('Quote', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'customer' => '',
            'customerName' => '',
            'address1' => '',
            'address2' => '',
            'city' => '',
            'state' => '',
            'zip' => '',
            'country' => '',
            'contactId' => '',
            'priceList' => '',
            'quoteNumber' => '',
            'deliveryDate' => '',
            'jobConvertedTo' => '',
            'jobType' => '',
            'requestDate' => '',
            'salesperson' => '',
            'description' => '',
            'isTemplate' => '',
            'status' => '',
            'eachOf' => '',
            'quoteAction' => '',
            'enteredBy' => '',
            'suspendCalculations' => '',
            'addCRMOpportunity' => '',
            'addCRMActivity' => '',
            'taxableCode' => '',
            'salesTax' => '',
            'shipToContact' => '',
            'billToContact' => '',
            'comboPrice' => '',
            'altCurrency' => '',
            'altCurrencyRate' => '',
            'quantity' => '',
            'quantityRemaining' => '',
            'updateJobInfoOnConvert' => '',
            'nextSequence' => '',
            'stateKey' => '',
        ];
    }
}