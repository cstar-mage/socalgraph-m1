<?php

class Blackbox_SummarInvoice_Block_Adminhtml_Summar_Invoice_View extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /** @var Blackbox_SummarInvoice_Model_Summar_Invoice */
    protected $summarInvoice = null;

    public function getInvoice()
    {
        return $this->summarInvoice;
    }

    public function initInvoice()
    {
        if ($this->summarInvoice) {
            return;
        }

        $filterData = $this->getFilterData();

        if (!$filterData->isEmpty()) {
            try {
                $this->summarInvoice = Mage::getModel('summar_invoice/summar_invoice');

                if ($filterData->getData('customer_id')) {
                    $customer = Mage::getModel('customer/customer')->load($filterData->getData('customer_id'));
                    if (!$customer->getId()) {
                        Mage::throwException('Wrong customer');
                    }
                } else {
                    $customer = null;
                }

                $this->summarInvoice->collectData($customer, $filterData->getData('from'), $filterData->getData('to'));

                Mage::register('current_invoice', $this->summarInvoice);
            } catch (Mage_Core_Exception $e) {
                $this->setError($e->getMessage());
            }
        }
    }

    protected function _beforeToHtml()
    {
        $this->initInvoice();
        return parent::_beforeToHtml();
    }

    protected function _toHtml()
    {
        if ($this->getFilterData()->isEmpty()) {
            return '';
        }
        if ($this->getError()) {
            return "<h2>Error</h2><div>{$this->getError()}</div>";
        }
        if ($this->summarInvoice->isEmpty()) {
            return '<div>No invoices found</div>';
        }
        return $this->getFormHtml();
    }
}