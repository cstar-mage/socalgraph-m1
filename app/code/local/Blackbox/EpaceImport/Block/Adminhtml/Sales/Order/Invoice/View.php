<?php

class Blackbox_EpaceImport_Block_Adminhtml_Sales_Order_Invoice_View extends Mage_Adminhtml_Block_Sales_Order_Invoice_View
{
    public function __construct()
    {
        parent::__construct();

        if ($invoiceId = $this->getInvoice()->getEpaceInvoiceId()) {
            $this->addButton('go_to_epace', [
                'label' => Mage::helper('sales')->__('Go to Epace'),
                'onclick' => 'popWin(\'' . $this->getEpaceUrl($invoiceId) . '\', \'_blank\', null)'
            ]);
        }
    }

    public function getEpaceUrl($invoiceId)
    {
        /** @var Blackbox_Epace_Helper_Api $api */
        $api = Mage::helper('epace/api');
        return $api->getHost() . '/epace/company:public/object/Invoice/detail/' . $invoiceId;
    }
}