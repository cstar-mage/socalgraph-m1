<?php

class Blackbox_Epace_Model_Epace_Invoice_Extra extends Blackbox_Epace_Model_Epace_Invoice_ChildAbstract
{
    /**
     * @var Blackbox_Epace_Model_Epace_Invoice_Extra_Type
     */
    protected $type;

    protected function _construct()
    {
        $this->_init('InvoiceExtra', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Invoice_Extra_Type
     */
    public function getType()
    {
        return $this->_getObject('type', 'invoiceExtraType', 'efi/invoice_extra_type');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Invoice_Extra_Type $type
     * @return $this
     */
    public function setType(Blackbox_Epace_Model_Epace_Invoice_Extra_Type $type)
    {
        $this->type = $type;

        return $this;
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'lineNum' => 'int',
            'invoice' => 'int',
            'price' => 'float',
            'memoCreated' => 'bool',
            'invoiceExtraType' => 'int',
            'manual' => 'bool',
            'jobPartReference' => 'string',
            'posted' => 'bool',
            'adjustedTotal' => 'float',
        ];
    }
}