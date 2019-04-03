<?php

/**
 * Associated Orders grid
 */
class Blackbox_EpaceImport_Block_Adminhtml_Estimate_View_Tab_Versions
    extends Blackbox_EpaceImport_Block_Adminhtml_Estimate_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('version');
        $this->setDefaultDir(Varien_Data_Collection::SORT_ORDER_DESC);
        $this->setId('versions');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
    }

    protected function _prepareCollection()
    {
        /** @var Mage_Sales_Model_Mysql4_Order_Grid_Collection $collection */
        $collection = Mage::getResourceModel('epacei/estimate_collection');
        $collection->addAttributeToFilter('estimate_number', $this->getEstimate()->getEstimateNumber());
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    /**
     * Retrieve estimate model instance
     *
     * @return Blackbox_EpaceImport_Model_Estimate
     */
    public function getEstimate()
    {
        return Mage::registry('current_estimate');
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/versionsGrid', array('_current'=>true));
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('sales')->__('Versions');
    }

    public function getTabTitle()
    {
        return Mage::helper('sales')->__('Versions');
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
