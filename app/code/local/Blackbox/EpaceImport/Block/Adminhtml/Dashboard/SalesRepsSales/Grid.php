<?php

class Blackbox_EpaceImport_Block_Adminhtml_Dashboard_SalesRepsSales_Grid extends Mage_Adminhtml_Block_Dashboard_Grid
{
    public function _construct()
    {
        parent::_construct();
        $this->setId('SalesRepsSalesGrid');
    }

    protected function _prepareCollection()
    {
        /** @var Blackbox_EpaceImport_Model_Resource_Reports_Customer_Collection $collection */
        $collection = Mage::getResourceModel('epacei/reports_customer_collection');
        $collection->calculateSalesRepsMonthlySales($this->getParam($this->getVarNameLimit(), $this->_defaultLimit));
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepares page sizes for dashboard grid with las 5 orders
     *
     * @return void
     */
    protected function _preparePage()
    {
        $this->getCollection()->setPageSize($this->getParam($this->getVarNameLimit(), $this->_defaultLimit));
        // Remove count of total orders $this->getCollection()->setCurPage($this->getParam($this->getVarNamePage(), $this->_defaultPage));
    }

    protected function _prepareColumns()
    {
        $this->addColumn('sales_rep', array(
            'header'    => $this->__('Sales Rep'),
            'sortable'  => false,
            'index'     => 'customer',
        ));

        $baseCurrencyCode = Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode();

        $this->addColumn('estimates', array(
            'header'    => $this->__('Estimates'),
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code' => $baseCurrencyCode,
            'index'     => 'estimates'
        ));

        $this->addColumn('estimates cost', array(
            'header'    => $this->__('Estimates Cost'),
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'estimates_cost'
        ));

        $this->addColumn('jobs', array(
            'header'    => $this->__('Jobs'),
            'align'     => 'right',
            'type'      => 'number',
            'sortable'  => false,
            'index'     => 'orders_count'
        ));
        $this->addColumn('total', array(
            'header'    => $this->__('Grand Total'),
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'grand_total'
        ));

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/customer/edit', array('id'=>$row->getId()));
    }
}