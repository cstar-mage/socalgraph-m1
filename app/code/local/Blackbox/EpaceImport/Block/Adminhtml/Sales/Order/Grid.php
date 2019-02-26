<?php

class Blackbox_EpaceImport_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
    protected function _prepareCollection()
    {
        /** @var Mage_Sales_Model_Resource_Order_Grid_Collection $collection */
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->getSelect()
            ->join([
                'o' => $collection->getResource()->getReadConnection()->select()->from($collection->getResource()->getTable('sales/order'), [
                    'entity_id',
                    'epace_job_id',
                    'customer' => 'CONCAT(COALESCE(customer_firstname, \'\'), \' \', COALESCE(customer_lastname, \'\'))',
                    'sales_person_id',
                    'amount_to_invoice',
                    'change_order_total',
                    'estimate_id',
                ])
            ], 'main_table.entity_id = o.entity_id', ['epace_job_id', 'customer', 'sales_person_id', 'amount_to_invoice', 'change_order_total', 'estimate_id'])
            ->joinLeft([
                'e' => $collection->getResource()->getReadConnection()->select()->from($collection->getResource()->getTable('epacei/estimate'), [
                    'estimate_id' => 'entity_id',
                    'estimate_price' => 'base_grand_total',
                    'estimate_currency_code',
                ])
            ], 'o.estimate_id = e.estimate_id', ['estimate_price', 'estimate_currency_code'])->columns(['delta' => 'o.amount_to_invoice - COALESCE(estimate_price, 0)'])
            ->joinLeft([
                's' => $collection->getResource()->getReadConnection()->select()->from($collection->getResource()->getTable('sales/shipment'), [
                    'order_id',
                    'shipments' => 'GROUP_CONCAT(epace_shipment_id SEPARATOR \', \')'
                ])->group('order_id')
            ], 'main_table.entity_id = s.order_id', ['shipments']);


        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('estimate_id', array(
            'header'=> Mage::helper('sales')->__('Estimate ID'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'estimate_id',
        ));

        $this->addColumn('job_id', array(
            'header'=> Mage::helper('sales')->__('Job ID'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'epace_job_id',
        ));

        $this->addColumn('shipments', array(
            'header'=> Mage::helper('sales')->__('Shipment IDs'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'shipments',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
                'escape'  => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('customer', array(
            'header'=> Mage::helper('sales')->__('Customer'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'customer'
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));

        $this->addColumn('sales_rep', array(
            'header'=> Mage::helper('sales')->__('Sales Rep'),
            'width' => '80px',
            'type'  => 'options',
            'index' => 'sales_person_id',
            'options' => Mage::helper('epacei')->getSalesRepsOptions()
        ));

        $this->addColumn('estimate_price', array(
            'header' => Mage::helper('sales')->__('Estimate Price'),
            'index' => 'estimate_price',
            'type'  => 'currency',
            'currency' => 'estimate_currency_code',
        ));

        $this->addColumn('amount_to_invoice', array(
            'header' => Mage::helper('sales')->__('Amount to Invoice'),
            'index' => 'amount_to_invoice',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));

        $this->addColumn('delta', array(
            'header' => Mage::helper('sales')->__('Delta'),
            'index' => 'delta',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));

        $this->addColumn('change_order_total', array(
            'header' => Mage::helper('sales')->__('Change order total'),
            'index' => 'change_order_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'*/sales_order/view'),
                            'field'   => 'order_id',
                            'data-column' => 'action',
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
                ));
        }
        $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }
}