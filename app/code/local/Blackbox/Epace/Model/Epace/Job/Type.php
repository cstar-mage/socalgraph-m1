<?php

class Blackbox_Epace_Model_Epace_Job_Type extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('JobType', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'description' => 'string',
            'jobNumberSequence' => '',
            'jacketType' => '',
            'jobNumberPrefix' => '',
            'active' => 'bool',
            'displayPartItems' => 'bool',
            'invoiceQuantity' => '',
            'useInvoiceDescriptionFrom' => '',
            'invoiceNumIsJobNum' => 'bool',
            'archiveDescription' => '',
            'displayDeposits' => 'bool',
            'autoApplyDeposits' => 'bool',
            'autoCloseJobs' => '',
            'autoEnterOverage' => 'bool',
            'autoEnterUnderage' => 'bool',
            'numberInvoiceOnAdd' => 'bool',
            'invoiceDateMethod' => '',
            'taxDistributionMethod' => '',
            'taxDistributionSource' => '',
            'commissionDistributionMethod' => '',
            'commissionDistributionSource' => '',
            'salesDistributionMethod' => '',
            'lineItemPriceOptions' => '',
            'salesTaxBasis' => '',
            'quoteItemInvoiceMethod' => '',
            'jobPartItemInvoiceDescription' => '',
            'finishedGoodInvoiceMethod' => '',
            'finishGoodInvoiceDescription' => '',
            'useManufacturingLocationPrefix' => 'bool',
            'invoiceLevelOptions' => '',
            'autoAddProduct' => 'bool',
            'consolidateVatTaxDistributions' => 'bool',
            'separateTaxDistibutionForRounding' => 'bool',
            'distributeTax' => 'bool',
            'billPartsTogetherAttribute' => 'bool',
        ];
    }
}