<?php

class Blackbox_EpaceImport_Block_Adminhtml_Customer_Edit_Tab_View_Sales extends Mage_Adminhtml_Block_Customer_Edit_Tab_View_Sales
{
    public function _beforeToHtml()
    {
        $this->_currency = Mage::getModel('directory/currency')
            ->load(Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE))
        ;

        $this->_collection = Mage::getResourceModel('epacei/sales_sale_collection')
            ->setCustomerFilter(Mage::registry('current_customer'))
            ->setOrderStateFilter(Mage_Sales_Model_Order::STATE_CANCELED, true);
        if (Mage::registry('current_customer')->getGroupId() == Mage::helper('epacei')->getWholesaleCustomerGroupId()) {
            $this->_collection->setSalesPersonFilter(Mage::registry('current_customer'));
        }
        $this->_collection->load();

        $this->_groupedCollection = array();

        foreach ($this->_collection as $sale) {
            if (!is_null($sale->getStoreId())) {
                $store      = Mage::app()->getStore($sale->getStoreId());
                $websiteId  = $store->getWebsiteId();
                $groupId    = $store->getGroupId();
                $storeId    = $store->getId();

                $sale->setWebsiteId($store->getWebsiteId());
                $sale->setWebsiteName($store->getWebsite()->getName());
                $sale->setGroupId($store->getGroupId());
                $sale->setGroupName($store->getGroup()->getName());
            }
            else {
                $websiteId  = 0;
                $groupId    = 0;
                $storeId    = 0;

                $sale->setStoreName(Mage::helper('customer')->__('Deleted Stores'));
            }

            $this->_groupedCollection[$websiteId][$groupId][$storeId] = $sale;
            $this->_websiteCounts[$websiteId] = isset($this->_websiteCounts[$websiteId]) ? $this->_websiteCounts[$websiteId] + 1 : 1;
        }

        return Mage_Adminhtml_Block_Template::_beforeToHtml();
    }
}