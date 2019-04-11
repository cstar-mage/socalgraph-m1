<?php

/**
 * Associated Orders grid
 */
class Blackbox_EpaceImport_Block_Adminhtml_Receivable_View_Tab_Invoices
    extends Mage_Adminhtml_Block_Sales_Invoice_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('receivable_invoices');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $receivable = $this->getReceivable();
        /** @var Mage_Sales_Model_Resource_Order_Invoice_Collection $collection */
        $collection = Mage::getResourceModel('sales/order_invoice_collection');
        if (!empty($receivable->getInvoiceNumber())) {
            $collection->addFieldToFilter('epace_invoice_number', $receivable->getInvoiceNumber());
        } else {
            $collection->getSelect()->where('FALSE');
        }
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareMassactionBlock()
    {
        $this->setChild('massaction', $this->getLayout()->createBlock($this->getMassactionBlockName()));
        return $this;
    }

    /**
     * Retrieve receivable model instance
     *
     * @return Blackbox_EpaceImport_Model_Receivable
     */
    public function getReceivable()
    {
        return Mage::registry('current_receivable');
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/invoicesGrid', array('_current'=>true));
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('sales')->__('Invoices');
    }

    public function getTabTitle()
    {
        return Mage::helper('sales')->__('Invoices');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
