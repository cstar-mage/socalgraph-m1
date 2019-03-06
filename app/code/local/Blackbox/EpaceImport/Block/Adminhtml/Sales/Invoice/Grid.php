<?php

class Blackbox_EpaceImport_Block_Adminhtml_Sales_Invoice_Grid extends Mage_Adminhtml_Block_Sales_Invoice_Grid
{
    public function _prepareCollection()
    {
        /** @var Mage_Sales_Model_Resource_Order_Invoice_Collection $collection */
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->getSelect()->joinInner([
            'o' => $collection->getResource()->getReadConnection()->select()
                ->from($collection->getResource()->getTable('sales/order'), [
                    'entity_id',
                    'customer_name' => 'CONCAT(COALESCE(customer_firstname, \'\'), \' \', COALESCE(customer_lastname, \'\'))',
                    'job_type'
                ])
            ], 'order_id = o.entity_id', ['customer_name', 'job_type']);
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    public function _prepareColumns()
    {
        $this->addColumnAfter('job_type', array(
            'header' => Mage::helper('sales')->__('Category'),
            'index' => 'job_type',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::helper('epacei')->getJobTypes(),
        ), 'order_created_at');

        $this->addColumnAfter('company', array(
            'header'    => Mage::helper('sales')->__('Company'),
            'index'     => 'customer_name',
            'type'      => 'text',
        ), 'job_type');
        return parent::_prepareColumns();
    }
}