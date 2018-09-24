<?php

class Blackbox_Pack_Model_Shipping_Shipping extends Mage_Shipping_Model_Shipping
{
    protected $items = [];

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        $this->_prepareRequestPacks($request);
        try {
            return parent::collectRates($request);
        } finally {
            $this->_restoreRequestPacks($request);
        }
    }

    protected function _prepareRequestPacks($request)
    {
        $packageQty = 0;
        foreach ($request->getAllItems() as $item)
        {
            if ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
                $qItem = $item->getQuoteItem() ?: $item->getQuote()->getItemById($item->getQuoteItemId());
                $type = 'a';
            } else {
                $qItem = $item;
                $type = 'q';
            }
            if ($qItem->getStepCount() > 1) {
                if (!isset($this->items[$type . $item->getId()])) {
                    $this->items[$type . $item->getId()] = [
                        'item' => $item,
                        'qty' => $item->getQty()
                    ];
                    $item->setQty(ceil($item->getQty() / (float)$qItem->getStepCount()));
                }
            }
            $packageQty += $item->getQty();
        }

        $request->setPackageQty($packageQty);
    }

    protected function _restoreRequestPacks($request)
    {
        foreach ($this->items as $id => $item) {
            $item['item']->setQty($item['qty']);
            unset($this->items[$id]);
        }
    }
}