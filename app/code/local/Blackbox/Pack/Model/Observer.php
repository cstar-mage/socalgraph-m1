<?php

class Blackbox_Pack_Model_Observer
{
    public function salesQuoteItemSetStepCount($observer)
    {
        $quoteItem = $observer->getQuoteItem();
        $product = $observer->getProduct();
        $quoteItem->setStepCount($product->getStepCount());
    }

    public function copyFieldsetQuoteItemToOrderItem($observer)
    {
        $orderItem = $observer->getTarget();
        if ($orderItem->getStepCount() > 1) {
            $orderItem->setQtyOrdered(ceil($orderItem->getQtyOrdered() / (float)$orderItem->getStepCount()));
        }
    }

    public function copyFieldsetOrdereItemToQuoteItem($observer)
    {
        $quoteItem = $observer->getTarget();
        if ($quoteItem->getStepCount() > 1) {
            $quoteItem->setQty($quoteItem->getQty() * $quoteItem->getStepCount());
        }
    }
}