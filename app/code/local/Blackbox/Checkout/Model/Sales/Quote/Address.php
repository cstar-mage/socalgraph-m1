<?php

class Blackbox_Checkout_Model_Sales_Quote_Address extends Mage_Sales_Model_Quote_Address
{
    /**
     * Get all available address items
     *
     * @return array
     */
    public function getAllItems($needAddressItems = false)
    {
        // We calculate item list once and cache it in three arrays - all items, nominal, non-nominal
        $cachedItems = $this->_nominalOnly ? 'nominal' : ($this->_nominalOnly === false ? 'nonnominal' : 'all');
        $returnAddressItems = $this->getQuote()->getIsMultiShipping() || count($this->getQuote()->getAllShippingAddresses()) > 1;

        $keySuffix = $returnAddressItems || $needAddressItems ? '_address' : '';
        $key = 'cached_items_' . $cachedItems . $keySuffix;

        if (!$this->hasData($key) || $needAddressItems && !$returnAddressItems) {
            // For compatibility  we will use $this->_filterNominal to divide nominal items from non-nominal
            // (because it can be overloaded)
            // So keep current flag $this->_nominalOnly and restore it after cycle
            $wasNominal = $this->_nominalOnly;
            $this->_nominalOnly = true; // Now $this->_filterNominal() will return positive values for nominal items

            $quoteItems = $this->getQuote()->getItemsCollection();
            $addressItems = $this->getItemsCollection();

            $items = array();
            $nominalItems = array();
            $nonNominalItems = array();
            if ($returnAddressItems || $needAddressItems) {
                foreach ($addressItems as $aItem) {
                    if ($aItem->isDeleted()) {
                        continue;
                    }

                    if (!$aItem->getQuoteItemImported()) {
                        $qItem = $this->getQuote()->getItemById($aItem->getQuoteItemId());
                        if ($qItem) {
                            $aItem->importQuoteItem($qItem);
                        }
                    }
                    $items[] = $aItem;
                    if ($this->_filterNominal($aItem)) {
                        $nominalItems[] = $aItem;
                    } else {
                        $nonNominalItems[] = $aItem;
                    }
                }
            } else {
                /*
                * For virtual quote we assign items only to billing address, otherwise - only to shipping address
                */
                $addressType = $this->getAddressType();
                $canAddItems = $this->getQuote()->isVirtual()
                    ? ($addressType == self::TYPE_BILLING)
                    : ($addressType == self::TYPE_SHIPPING);

                if ($canAddItems) {
                    foreach ($quoteItems as $qItem) {
                        if ($qItem->isDeleted()) {
                            continue;
                        }
                        if ($this->hasItemQty($qItem->getId())) {
                            $item = Mage::getModel('sales/quote_item')->setData($qItem->getData());
                            $item->setQty($this->getItemQty($qItem->getId()));
                        }
                        $items[] = $qItem;
                        if ($this->_filterNominal($qItem)) {
                            $nominalItems[] = $qItem;
                        } else {
                            $nonNominalItems[] = $qItem;
                        }
                    }
                }
            }

            // Cache calculated lists
            $this->setData('cached_items_all' . $keySuffix, $items);
            $this->setData('cached_items_nominal' . $keySuffix, $nominalItems);
            $this->setData('cached_items_nonnominal' . $keySuffix, $nonNominalItems);

            $this->_nominalOnly = $wasNominal; // Restore original value before we changed it
        }

        $items = $this->getData($key);
        return $items;
    }

    public function addItem(Mage_Sales_Model_Quote_Item_Abstract $item, $qty = null)
    {
        parent::addItem($item, $qty);
        $this->clearCachedItems();
        return $this;
    }

    public function clearCachedItems()
    {
        $keys = [
            'cached_items_all',
            'cached_items_nominal',
            'cached_items_nonnominal'
        ];
        foreach ($keys as $key) {
        $this->unsetData($key);
        $this->unsetData($key . '_address');
        }
        return $this;
    }

    protected function _basicCheck()
    {
        if ($this->getStorelocatorId()) {
            $storelocator = Mage::getModel('storelocator/storelocator')->load($this->getStorelocatorId());
            if (!$storelocator->getId()) {
                $this->addError(Mage::helper('customer')->__('Wrong storelocator specified'));
            }
            $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
            if (!$customer->getVisibleStores() || !in_array($storelocator->getId(), explode(',', $customer->getVisibleStores()))) {
                $this->addError(Mage::helper('customer')->__('Wrong storelocator specified'));
            }
        } else {
            parent::_basicCheck();
        }
    }
}