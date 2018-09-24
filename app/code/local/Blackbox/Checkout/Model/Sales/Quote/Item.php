<?php

class Blackbox_Checkout_Model_Sales_Quote_Item extends Mage_Sales_Model_Quote_Item
{
    /**
     * Declare quote item quantity
     *
     * @param float $qty
     * @return Mage_Sales_Model_Quote_Item
     */
    public function setQty($qty)
    {
        $qty = Mage::app()->getLocale()->getNumber($qty);
        $oldQty = $this->_getData('qty');
        $this->setData('qty', $qty);

        if (count($this->getQuote()->getAllShippingAddresses()) <= 1 || $qty != 0) {
            Mage::dispatchEvent('sales_quote_item_qty_set_after', array('item' => $this));

            if ($this->getQuote() && $this->getQuote()->getIgnoreOldQty()) {
                return $this;
            }
            if ($this->getUseOldQty()) {
                $this->setData('qty', $oldQty);
            }
        }

        return $this;
    }
}