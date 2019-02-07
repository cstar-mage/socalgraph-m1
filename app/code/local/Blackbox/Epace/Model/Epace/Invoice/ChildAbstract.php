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
        if (is_null($this->invoice)) {
            $this->invoice = false;
            if ($this->getData('invoice')) {
                $invoice = Mage::getModel('efi/invoice')->load($this->getData('invoice'));
                if ($invoice->getId()) {
                    $this->invoice = $invoice;
                }
            }
        }

        return $this->invoice;
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