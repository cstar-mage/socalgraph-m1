<?php

class Blackbox_Epace_Model_Epace_Invoice_CommDist extends Blackbox_Epace_Model_Epace_Invoice_ChildAbstract
{
    protected function _construct()
    {
        $this->_init('InvoiceCommDist', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'invoice' => 'int',
            'amount' => '',
            'commBase' => '',
            'memoCreated' => 'bool',
            'salesPerson' => '',
            'lockCommBase' => 'bool',
            'lockAmount' => 'bool',
            'salesCategory' => '',
            'commissionRate' => '',
            'manual' => 'bool',
            'posted' => 'bool',
            'amountAdjustment' => '',
            'adjustedTotal' => '',
        ];
    }
}