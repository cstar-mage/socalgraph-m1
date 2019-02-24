<?php

abstract class Blackbox_Epace_Model_Epace_Invoice_ChildAbstract extends Blackbox_Epace_Model_Epace_AbstractObject
{
    /** @var Blackbox_Epace_Model_Epace_Invoice */
    protected $invoice = null;

    /**
     * @return Blackbox_Epace_Model_Epace_Invoice|bool
     */
    public function getInvoice()
    {
        return $this->_getObject('invoice', 'invoice', 'efi/invoice');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Invoice $invoice
     * @return $this
     */
    public function setInvoice(Blackbox_Epace_Model_Epace_Invoice $invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }
}