<?php

class Blackbox_Pack_Model_Sales_Order_Invoice_Item extends Mage_Sales_Model_Order_Invoice_Item
{
    public function getItemsQty()
    {
        return $this->getQty() * ($this->getStepCount() ?: 1);
    }
}