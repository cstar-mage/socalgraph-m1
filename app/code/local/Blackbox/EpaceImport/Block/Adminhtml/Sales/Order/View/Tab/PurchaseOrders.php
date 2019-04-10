<?php

class Blackbox_EpaceImport_Block_Adminhtml_Sales_Order_View_Tab_PurchaseOrders
    extends Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir(Varien_Data_Collection::SORT_ORDER_DESC);
        $this->setId('order_purchase_orders');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
    }

    protected function _prepareMassaction()
    {
    }

    protected function _prepareCollection()
    {
        /** @var Mage_Sales_Model_Mysql4_Order_Grid_Collection $collection */
        $collection = Mage::getResourceModel('epacei/purchaseOrder_collection');

        $select = $collection->getConnection()->select()->from($collection->getTable('epacei/purchase_order_item'), 'purchase_order_id')
            ->where('order_id = ?', $this->getOrder()->getId())
            ->group('purchase_order_id');
        $collection->getSelect()->where('entity_id IN (' . $select . ')');

        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    /**
     * Retrieve estimate model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/epace_purchaseOrder/orderPurchaseOrdersGrid', array('_current'=>true));
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('sales')->__('Purchase Orders');
    }

    public function getTabTitle()
    {
        return Mage::helper('sales')->__('Purchase Orders');
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