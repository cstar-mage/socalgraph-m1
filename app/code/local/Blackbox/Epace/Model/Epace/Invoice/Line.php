<?php

/**
 * @method float getQtyOrdered()
 * @method float getQtyInvoiced()
 * @method float getQtyShipped()
 *
 * Class Blackbox_Epace_Model_Epace_Invoice_Line
 */
class Blackbox_Epace_Model_Epace_Invoice_Line extends Blackbox_Epace_Model_Epace_Invoice_ChildAbstract
{
    protected function _construct()
    {
        $this->_init('InvoiceLine', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'invoice' => 'int',
            'lineNum' => '',
            'qtyInvoiced' => '',
            'qtyOrdered' => '',
            'qtyShipped' => '',
            'unitPrice' => '',
            'totalPrice' => '',
            'memoCreated' => 'bool',
            'lineType' => '',
            'salesCategory' => '',
            'uom' => '',
            'description' => '',
            'flatPrice' => 'bool',
            'jobPartReference' => '',
            'posted' => 'bool',
            'adjustedTotal' => '',
        ];
    }
}