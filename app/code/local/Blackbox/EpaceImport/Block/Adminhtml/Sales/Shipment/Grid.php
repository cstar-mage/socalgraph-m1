<?php

class Blackbox_EpaceImport_Block_Adminhtml_Sales_Shipment_Grid extends Mage_Adminhtml_Block_Sales_Shipment_Grid
{
    public function _prepareCollection()
    {
        /** @var Mage_Sales_Model_Resource_Order_Shipment_Collection $collection */
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->getSelect()->joinInner([
            'o' => $collection->getResource()->getReadConnection()->select()
                ->from($collection->getResource()->getTable('sales/order'), [
                    'entity_id',
                    'customer_name' => 'CONCAT(COALESCE(customer_firstname, \'\'), \' \', COALESCE(customer_lastname, \'\'))',
                    'job_type',
                    'estimate_id'
                ])
            ], 'order_id = o.entity_id', ['customer_name', 'job_type', 'estimate_id'])
            ->joinLeft([
                'e' => $collection->getResource()->getReadConnection()->select()
                ->from($collection->getResource()->getTable('epacei/estimate'), [
                    'entity_id',
                    'estimate_number'
                ])
            ], 'o.estimate_id = e.entity_id', ['estimate_number']);
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    public function _prepareColumns()
    {
        $this->addColumnAfter('estimate_number', [
            'header' => Mage::helper('sales')->__('Estimate #'),
            'index' => 'estimate_number',
            'type' => 'text',
        ], 'created_at');

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