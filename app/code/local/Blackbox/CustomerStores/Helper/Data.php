<?php

class Blackbox_CustomerStores_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function formatAddressItem($item)
    {
        if ($item instanceof Magestore_Storelocator_Model_Storelocator) {
            return $this->_formatStorelocator($item);
        } else if ($item->getStorelocatorId()) {
            $storelocator = Mage::getModel('storelocator/storelocator')->load($item->getStorelocatorId());
            if ($storelocator->getId()) {
                return $this->_formatStorelocator($storelocator);
            }
        } else if ($item instanceof Mage_Customer_Model_Address_Abstract) {
            return $item->format('text');
        }
    }

    /**
     * @param string $name
     * @return Magestore_Storelocator_Model_Storelocator
     */
    public function getStorelocatorByName($name)
    {
        return Mage::getModel('storelocator/storelocator')->load($name, 'name');
    }

    public function isStorelocatorVisible($storelocator)
    {
        $storelocatorIds = Mage::getSingleton('customer/session')->getCustomer()->getVisibleStores();
        return $storelocatorIds && in_array($storelocator->getId(), explode(',', $storelocatorIds));
    }

    protected function _formatStorelocator($item)
    {
        return implode(', ', array_filter([
            $item->getName(),
            $item->getAddress(),
            $item->getCity(),
            $item->getCountry(),
            $item->getZipcode(),
            $item->getState(),
            $item->getEmail(),
            $item->getPhone() ? 'T: ' . $item->getPhone() : ''
        ]));
    }
}