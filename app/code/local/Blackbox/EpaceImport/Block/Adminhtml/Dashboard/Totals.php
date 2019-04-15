<?php

class Blackbox_EpaceImport_Block_Adminhtml_Dashboard_Totals extends Blackbox_RolesPermissions_Block_Adminhtml_Dashboard_Totals
{
    protected function _prepareLayout()
    {
        if (!Mage::helper('core')->isModuleEnabled('Mage_Reports')) {
            return $this;
        }
        $isFilter = $this->getRequest()->getParam('store') || $this->getRequest()->getParam('website') || $this->getRequest()->getParam('group');
        $period = $this->getRequest()->getParam('period', '2y');

        /* @var $collection Blackbox_EpaceImport_Model_resource_Reports_Order_Collection */
        $collection = Mage::getResourceModel('epacei/reports_order_collection')
            ->addCreateAtPeriodFilter($period)
            ->calculateTotals($isFilter);

        if ($this->getRequest()->getParam('store')) {
            $collection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
        } else if ($this->getRequest()->getParam('website')){
            $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            $collection->addFieldToFilter('store_id', array('in' => $storeIds));
        } else if ($this->getRequest()->getParam('group')){
            $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
            $collection->addFieldToFilter('store_id', array('in' => $storeIds));
        } elseif (!$collection->isLive()) {
            $collection->addFieldToFilter('store_id',
                array('eq' => Mage::app()->getStore(Mage_Core_Model_Store::ADMIN_CODE)->getId())
            );
        }

        $collection->load();

        $totals = $collection->getFirstItem();

        if (Mage::helper('rolespermissions')->canViewPrices()) {
            $this->addTotal($this->__('Estimate Price'), $totals->getEstimatePrice());
            $this->addTotal($this->__('Amount To Invoice'), $totals->getAmountToInvoice());
        }
        $this->addTotal($this->__('Quantity'), $totals->getQuantity()*1, true);
    }
}