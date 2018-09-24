<?php

class Blackbox_Checkout_Block_Checkout_Onepage_Shipping_Products extends Mage_Checkout_Block_Onepage_Abstract
{
    /**
     * @return Mage_Sales_Model_Quote_Address_Item[]|Mage_Sales_Model_Quote_Item[]
     */
    public function getItems()
    {
        $result = [];

        if (count($this->getQuote()->getAllShippingAddresses()) == 1) {
            return $this->getQuote()->getAllVisibleItems();
        }
        $aItems = $this->getAddress()->getAllVisibleItems();

        $qItems = $this->getQuote()->getAllVisibleItems();

        foreach ($qItems as $qItem) {
            $aItem = $this->_getAddressItem($aItems, $qItem);
            if ($aItem) {
                $result[] = $aItem;
            } else {
                $result[] = Mage::getModel('sales/quote_address_item')->setQty(0)->importQuoteItem($qItem);
            }
        }

        return $result;
    }

    /**
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        return $this->getData('address') ?: $this->getParentBlock()->getAddress();
    }

    /**
     * @return int
     */
    public function getShippingId()
    {
        return $this->getParentBlock()->getShippingId();
    }

    /**
     * @param Mage_Sales_Model_Quote_Address_Item[] $addressItems
     * @param int $quoteItemId
     */
    protected function _getAddressItem($addressItems, $quoteItem)
    {
        if (is_object($quoteItem)) {
            $quoteItem = $quoteItem->getId();
        }
        foreach ($addressItems as $item) {
            if ($item->getQuoteItemId() == $quoteItem) {
                return $item;
            }
        }
    }
}