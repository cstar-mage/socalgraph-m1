<?php

class Blackbox_CustomerStores_Model_Resource_Address_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('customer/address');
    }

    public function init($customer)
    {
        /** @var Blackbox_CustomerStores_Model_Resource_Address_Collection_Inner $collection */
        $collection = Mage::getResourceModel('customer_stores/address_collection_inner');

        $this->_select = $this->getResource()->getReadConnection()->select()->from($collection->extractSelect($customer));
    }

    public function getMainTable()
    {
        if ($this->_mainTable === null) {
            $this->setMainTable($this->getResource()->getEntityTable());
        }

        return $this->_mainTable;
    }

    protected function _initSelect()
    {
        $this->getSelect()->from(array('main_table' => $this->getMainTable()));
        return $this;
    }
}