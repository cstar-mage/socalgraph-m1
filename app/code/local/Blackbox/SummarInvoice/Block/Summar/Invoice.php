<?php

class Blackbox_SummarInvoice_Block_Summar_Invoice extends Mage_Core_Block_Template
{
    /**
     * @return Blackbox_SummarInvoice_Model_Summar_Invoice|null
     */
    public function getInvoice()
    {
        return Mage::registry('current_invoice');
    }

    public function getTotals($order)
    {
        return $this->getChild('order_totals')->setOrder($order)->toHtml();
    }
}