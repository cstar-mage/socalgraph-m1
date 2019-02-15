<?php

class Blackbox_EpaceImport_Block_Adminhtml_Dashboard_Sales extends Mage_Adminhtml_Block_Dashboard_Sales
{
    protected function _prepareLayout()
    {
        if (!Mage::helper('core')->isModuleEnabled('Mage_Reports')) {
            return $this;
        }
        $isFilter = $this->getRequest()->getParam('store') || $this->getRequest()->getParam('website') || $this->getRequest()->getParam('group');

        $collection = Mage::getResourceModel('epacei/reports_order_collection')
            ->calculateMonthlySales($isFilter);
        $estimateCollection = Mage::getResourceModel('epacei/reports_estimate_collection')
            ->calculateMonthlySales($isFilter);
        $profitCollection = Mage::getResourceModel('epacei/reports_order_collection')
            ->calculateProfitPerJob($isFilter);
        $receivableCollection = Mage::getResourceModel('epacei/reports_receivable_collection')
            ->calculateLast90DaysSales($isFilter);

        if ($this->getRequest()->getParam('store')) {
            $collection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
            $profitCollection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
            $estimateCollection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
            $receivableCollection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
        } else if ($this->getRequest()->getParam('website')){
            $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            $collection->addFieldToFilter('store_id', array('in' => $storeIds));
            $estimateCollection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
            $profitCollection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
            $receivableCollection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
        } else if ($this->getRequest()->getParam('group')){
            $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
            $collection->addFieldToFilter('store_id', array('in' => $storeIds));
            $estimateCollection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
            $profitCollection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
            $receivableCollection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
        }

        $collection->load();
        $sales = $collection->getFirstItem();

        $estimateCollection->load();
        $estimates = $estimateCollection->getFirstItem();

        $profitCollection->load();
        $profit = $profitCollection->getFirstItem();

        $receivableCollection->load();
        $receivables = $receivableCollection->getFirstItem();

        $this->addTotal($this->__('Monthly Sales'), $sales->getMonthly());
        $this->addTotal($this->__('Monthly Estimates'), $estimates->getMonthly());
        $this->addTotal($this->__('Profit Per Job'), $profit->getAverage());
        $this->addTotal($this->__('90 Days Receivables'), $receivables->getSum());
    }
}