<?php

class Blackbox_Epace_Model_Epace_Customer extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('Customer', 'id');
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
            'city' => '',
            'customerType' => '',
            'contactFirstName' => '',
            'contactLastName' => '',
            'country' => '',
            'creditLimit' => '',
            'csr' => '',
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
            'stateKey' => '',
        ];
    }
}