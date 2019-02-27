<?php

class Blackbox_EpaceImport_Block_Adminhtml_Customer_Edit_Tab_Shipments extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_shipments_grid');
        $this->setDefaultSort('created_at', 'desc');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('sales/order_shipment_grid_collection')
            ->addFieldToFilter(['customer_id', 'sales_person_id'], [$customerId = Mage::registry('current_customer')->getId(), $customerId]);
        $collection->getSelect()->join(
            ['s' => $collection->getResource()->getReadConnection()->select()->from($collection->getResource()->getTable('sales/shipment'), ['entity_id', 'sales_person_id', 'customer_id'])],
            'main_table.entity_id = s.entity_id',
            ['customer_id', 'sales_person_id']
        );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('sales')->__('Shipment #'),
            'index'     => 'increment_id',
            'type'      => 'text',
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('sales')->__('Date Shipped'),
            'index'     => 'created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('order_increment_id', array(
            'header'    => Mage::helper('sales')->__('Order #'),
            'index'     => 'order_increment_id',
            'type'      => 'text',
        ));

        $this->addColumn('order_created_at', array(
            'header'    => Mage::helper('sales')->__('Order Date'),
            'index'     => 'order_created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));

        $this->addColumn('total_qty', array(
            'header' => Mage::helper('sales')->__('Total Qty'),
            'index' => 'total_qty',
            'type'  => 'number',
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('sales')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('sales')->__('View'),
                        'url'     => array('base'=>'*/sales_shipment/view'),
                        'field'   => 'shipment_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true
            ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/sales_shipment/view', array('shipment_id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/shipments', array('_current' => true));
    }

}
