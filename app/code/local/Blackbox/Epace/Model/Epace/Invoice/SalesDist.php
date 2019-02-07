<?php

class Blackbox_Epace_Model_Epace_Invoice_SalesDist extends Blackbox_Epace_Model_Epace_Invoice_ChildAbstract
{
    protected function _construct()
    {
        $this->_init('InvoiceSalesDist', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'invoice' => 'int',
            'amount' => '',
            'salesCategory' => '',
            'memoCreated' => 'bool',
            'manual' => 'bool',
            'jobPartReference' => '',
            'posted' => 'bool',
            'taxBase' => '',
            'commBase' => '',
            'adjustedTotal' => '',
        ];
    }
}