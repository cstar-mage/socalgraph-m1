<?php

class Blackbox_SummarInvoice_Adminhtml_Report_Summar_InvoiceController extends Mage_Adminhtml_Controller_Report_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_publicActions[] = 'printInvoice';
    }

    public function indexAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Summar Invoice'));

        $this->_initAction()
            ->_setActiveMenu('report/sales/summar_invoice')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Summar Invoice'), Mage::helper('adminhtml')->__('Summar Invoice'));

        $filterFormBlock = $this->getLayout()->getBlock('filter.form');
        $invoiceViewBlock = $this->getLayout()->getBlock('invoice.view');

        $this->_initReportAction(array(
            $filterFormBlock,
            $invoiceViewBlock
        ));

        $invoiceViewBlock->initInvoice();

        $this->renderLayout();
    }

    public function sendEmailAction()
    {
        $this->loadLayout();
        $invoiceViewBlock = $this->getLayout()->getBlock('invoice.view');

        $this->_initReportAction(array(
            $invoiceViewBlock
        ));

        $invoiceViewBlock->initInvoice();

        /** @var Blackbox_SummarInvoice_Model_Summar_Invoice $invoice */
        $invoice = Mage::registry('current_invoice');
        if ($invoice) {
            if ($invoice->sendEmail()) {
                Mage::getSingleton('adminhtml/session')->addSuccess('The email was sent.');
            }
        }

        $this->_redirect('*/*/index', array('_current' => true));
    }

    public function printInvoiceAction()
    {
        if ($invoiceId = $this->getRequest()->getParam('invoice_id')) {
            if ($invoice = Mage::getModel('sales/order_invoice')->load($invoiceId)) {
                $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf(array($invoice));
                $this->_prepareDownloadResponse('invoice'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').
                    '.pdf', $pdf->render(), 'application/pdf');
            }
        }
        else {
            $this->_forward('noRoute');
        }
    }
}