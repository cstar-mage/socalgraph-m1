<?php

/**
 * Associated Orders grid
 */
class Blackbox_EpaceImport_Block_Adminhtml_Receivable_View_Tab_Invoices
    extends Blackbox_EpaceImport_Block_Adminhtml_Sales_Invoice_Grid//Mage_Adminhtml_Block_Sales_Invoice_Grid
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
        $collection->getSelect()->joinInner([
            'o' => $collection->getResource()->getReadConnection()->select()
                ->from($collection->getResource()->getTable('sales/order'), [
                    'entity_id',
                    'customer_name' => 'CONCAT(COALESCE(customer_firstname, \'\'), \' \', COALESCE(customer_lastname, \'\'))',
                    'job_type'
                ])
        ], 'order_id = o.entity_id', ['customer_name', 'job_type'])
        ->joinInner([
            'g' => $collection->getResource()->getReadConnection()->select()
            ->from ($collection->getResource()->getTable('sales/invoice_grid'), [
                'entity_id',
                'order_increment_id',
                'order_created_at',
                'billing_name'
            ])
        ], 'main_table.entity_id = g.entity_id', ['order_increment_id', 'order_created_at', 'billing_name']);
        $this->setCollection($collection);

        $collection->addFieldToFilter('receivable_id', $receivable->getId());
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
