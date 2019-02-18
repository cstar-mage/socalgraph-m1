<?php

class Blackbox_EpaceImport_Block_Adminhtml_Dashboard_Shipping_Grid extends Mage_Adminhtml_Block_Dashboard_Grid
{
    public function _construct()
    {
        parent::_construct();
        $this->setId('ReceivableOutstandingGrid');
    }

    protected function _prepareCollection()
    {
        /** @var Blackbox_EpaceImport_Model_Resource_Reports_Receivable_Collection $collection */
        $collection = Mage::getResourceModel('sales/order_shipment_collection');
        $collection->addOrder('created_at', Varien_Data_Collection::SORT_ORDER_DESC);

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
        $this->addColumn('shipment', array(
            'header'    => $this->__('Shipment'),
            'sortable'  => false,
            'index'     => 'increment_id',
        ));

        $this->addColumn('customer', array(
            'header'    => $this->__('Customer'),
            'sortable'  => false,
            'index'     => 'customer_id',
            'frame_callback' => array($this, 'customerColumnFrameCallback')
        ));

        $this->addColumn('total_qty', array(
            'header'    => $this->__('Quantity'),
            'align'     => 'right',
            'type'      => 'number',
            'sortable'  => false,
            'index'     => 'total_qty'
        ));

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/sales_shipment/view', array('shipment_id'=>$row->getId()));
    }

    public function customerColumnFrameCallback($value, $row, $column, $isExport) {
        $customer = Mage::getModel('customer/customer')->load($row->getCustomerId());
        return implode(' ', array_filter([
            $customer->getFirstname(),
            $customer->getMiddlename(),
            $customer->getLastname()
        ]));
    }
}