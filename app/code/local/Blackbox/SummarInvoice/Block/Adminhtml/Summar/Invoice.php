<?php

class Blackbox_SummarInvoice_Block_Adminhtml_Summar_Invoice extends Mage_Adminhtml_Block_Widget_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_summar_invoice';
        $this->_blockGroup = 'blackbox_report';
        $this->_headerText = Mage::helper('summar_invoice')->__('Summar Invoice');
        parent::__construct();
        $this->_removeButton('add');
        $this->addButton('filter_form_submit', array(
            'label'     => Mage::helper('reports')->__('Show Summar Invoice'),
            'onclick'   => 'filterFormSubmit()'
        ));
        $this->addButton('send_email', array(
            'label'     => Mage::helper('reports')->__('Send Email'),
            'onclick'   => 'document.location=\'' . $this->getUrl('*/*/sendEmail', array('_current' => true)) .  '\''
        ));
    }

    public function _beforeToHtml()
    {
        $invoice = $this->getInvoice();
        if (!$invoice || !$invoice->getOrder()) {
            $this->removeButton('send_email');
        }
    }

    public function getInvoiceHtml()
    {
        return $this->getChildHtml('invoice.view');
    }

    /**
     * @return Blackbox_SummarInvoice_Model_Summar_Invoice|null
     */
    public function getInvoice()
    {
        return Mage::registry('current_invoice');
    }
}