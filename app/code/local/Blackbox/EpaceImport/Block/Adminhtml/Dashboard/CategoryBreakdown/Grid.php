<?php

class Blackbox_EpaceImport_Block_Adminhtml_Dashboard_CategoryBreakdown_Grid extends Mage_Adminhtml_Block_Dashboard_Grid
{
    public function _construct()
    {
        parent::_construct();
        $this->setId('categoryBreakdownGrid');
    }

    protected function _prepareCollection()
    {
        $isFilter = $this->getRequest()->getParam('store') || $this->getRequest()->getParam('website') || $this->getRequest()->getParam('group');

        /** @var Blackbox_EpaceImport_Model_resource_Reports_Order_Collection $collection */
        $collection = Mage::getResourceModel('epacei/reports_order_collection');
        $collection->calculateCategoryBreakdown($isFilter);

        if ($this->getRequest()->getParam('store')) {
            $collection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
        } else if ($this->getRequest()->getParam('website')){
            $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            $collection->addFieldToFilter('store_id', array('in' => $storeIds));
        } else if ($this->getRequest()->getParam('group')){
            $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
            $collection->addFieldToFilter('store_id', array('in' => $storeIds));
        }

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
        $this->addColumn('category', array(
            'header'    => $this->__('Category Breakdown'),
            'sortable'  => false,
            'index'     => 'category',
        ));

        $baseCurrencyCode = Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode();

        $this->addColumn('total', array(
            'header'    => $this->__('Total Sales'),
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'total'
        ));

        $this->addColumn('count', array(
            'header'    => $this->__('Number of Jobs'),
            'align'     => 'right',
            'type'      => 'number',
            'sortable'  => false,
            'index'     => 'count'
        ));

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }
}