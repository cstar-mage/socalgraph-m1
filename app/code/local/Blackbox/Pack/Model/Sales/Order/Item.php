<?php

class Blackbox_Pack_Model_Sales_Order_Item extends Blackbox_OrderApproval_Model_Sales_Order_Item//Mage_Sales_Model_Order_Item
{
    public function getItemsQtyOrdered()
    {
        return $this->getQtyOrdered() * ($this->getStepCount() ?: 1);
    }

    public function getItemsQtyInvoiced()
    {
        return $this->getQtyInvoiced() * ($this->getStepCount() ?: 1);
    }

    public function getItemsQtyShipped()
    {
        return $this->getQtyShipped() * ($this->getStepCount() ?: 1);
    }

    public function getItemsQtyCanceled()
    {
        return $this->getQtyCanceled() * ($this->getStepCount() ?: 1);
    }

    public function getItemsQtyRefunded()
    {
        return $this->getQtyRefunded() * ($this->getStepCount() ?: 1);
    }
}