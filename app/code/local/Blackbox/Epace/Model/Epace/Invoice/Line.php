<?php

/**
 * @method float getLineNum()
 * @method float getQtyInvoiced()
 * @method float getQtyOrdered()
 * @method float getQtyShipped()
 * @method float getUnitPrice()
 * @method float getTotalPrice()
 * @method bool getMemoCreated()
 * @method int getLineType()
 * @method int getSalesCategory()
 * @method string getUom()
 * @method string getDescription()
 * @method bool getFlatPrice()
 * @method int getJobPartReference()
 * @method bool getPosted()
 * @method float getAdjustedTotal()
 *
 * Class Blackbox_Epace_Model_Epace_Invoice_Line
 */
class Blackbox_Epace_Model_Epace_Invoice_Line extends Blackbox_Epace_Model_Epace_Invoice_ChildAbstract
{
    const LINE_TYPE_PRICE = 1;
    const LINE_TYPE_QUOTE_ITEM = 2;
    const LINE_TYPE_DESCRIPTION = 4;

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