<?php

class Blackbox_EpaceImport_Block_Adminhtml_Sales_Order_View_Tab_Estimates
    extends Blackbox_EpaceImport_Block_Adminhtml_Estimate_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('version');
        $this->setDefaultDir(Varien_Data_Collection::SORT_ORDER_DESC);
        $this->setId('order_estimates');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
    }

    protected function _prepareMassaction()
    {
    }

    protected function _prepareCollection()
    {
        /** @var Mage_Sales_Model_Mysql4_Order_Grid_Collection $collection */
        $collection = Mage::getResourceModel('epacei/estimate_collection');

        $order = $this->getOrder();
        if ($order->getEstimateId()) {
            $estimate = Mage::getModel('epacei/estimate')->load($order->getEstimateId());
        }

        if (isset($estimate) && $estimate->getId()) {
            $collection->addAttributeToFilter('estimate_number', $estimate->getEstimateNumber());
        } else {
            $collection->getSelect()->where('FALSE');
        }
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
        return $this->getUrl('*/epace_estimate/orderEstimatesGrid', array('_current'=>true));
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('sales')->__('Estimates');
    }

    public function getTabTitle()
    {
        return Mage::helper('sales')->__('Estimates');
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