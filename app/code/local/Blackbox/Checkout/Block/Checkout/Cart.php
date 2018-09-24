<?php

class Blackbox_Checkout_Block_Checkout_Cart extends Mage_Checkout_Block_Cart
{
    public function getAbstractItemHtml(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Item) {
            return $this->getItemHtml($item);
        } else if ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
            return $this->getAddressItemHtml($item);
        }
    }

    public function getAddressItemHtml(Mage_Sales_Model_Quote_Address_Item $item)
    {
        if (!$item->getQuoteItem()) {
            $item->setQuoteItem($item->getQuote()->getItemById($item->getQuoteItemId()));
        }
        $renderer = $this->getItemRenderer($item->getQuoteItem()->getProductType())->setItem($item);
        return $renderer->toHtml();
    }
}