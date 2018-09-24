<?php

class Blackbox_SummarInvoice_Block_Adminhtml_Summar_Invoice_View_Info extends Mage_Adminhtml_Block_Sales_Order_View_Info
{
    /**
     * @return Blackbox_SummarInvoice_Model_Summar_Invoice
     */
    public function getInvoice()
    {
        return Mage::registry('current_invoice');
    }
}