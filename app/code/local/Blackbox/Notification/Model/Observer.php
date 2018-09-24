<?php

class Blackbox_Notification_Model_Observer
{
    protected $itemsQty = array();

    public function quoteSubmitBefore($observer)
    {
        $items = $observer->getEvent()->getQuote()->getAllItems();

        foreach ($items as $item)
        {
            $stockItem = $item->getProduct()->getStockItem();
            if (!$stockItem->getManageStock()) {
                continue;
            }
            if ((int)$stockItem->getQty()) {
                $this->itemsQty[$stockItem->getId()] = $stockItem->getQty();
            }
        }
    }

    public function checkoutSubmitAllAfter($observer)
    {
        $this->_processQuoteSuccessLowStock();
        if ($observer->getOrder()) {
            $this->_processQuoteSuccessOrderedQtyExcced($observer->getOrder());
            $this->_processCreateOrderRequiringApproval($observer->getOrder());
        }

        $orders = $observer->getOrders();
        if (!empty($orders)) {
            foreach ($orders as $order) {
                $this->_processQuoteSuccessOrderedQtyExcced($order);
                $this->_processCreateOrderRequiringApproval($order);
            }
        }
    }

    public function stockItemSaveAfter($observer)
    {
        $item = $observer->getEvent()->getItem(); /* @var Mage_CatalogInventory_Model_Stock_Item $item*/

        if ($item->getOrigData('qty') === null) {
            if (isset($this->itemsQty[$item->getId()])) {
                $item->setOrigData('qty', $this->itemsQty[$item->getId()]);
            } else {
                return;
            }
        }

        unset($this->itemsQty[$item->getId()]);

        $this->_getValidator()->processStockItem($item);
    }

    public function checkoutCartAddProductComplete($observer)
    {
        $product = $observer->getEvent()->getProduct();

        $this->_getValidator()->processAddedToCartProduct($product);
    }

    public function salesOrderApprovalRegister($observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getOrder();

        if (Mage::helper('order_approval')->isOrderApproved($order)) {
            // Start store emulation process
            /** @var $appEmulation Mage_Core_Model_App_Emulation */
            $appEmulation = Mage::getSingleton('core/app_emulation');
            $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($order->getStoreId());

            try {
                $this->_getValidator()->processOrderApproved($order);
            } finally {
                // Stop store emulation process
                $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            }
        }
    }

    public function editOrder($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $history = $observer->getEvent()->getHistory();

        $this->_getValidator()->processEditOrder($order, $history);
    }

    public function helpdeskTicketNew($observer)
    {
        $ticket = $observer->getEvent()->getTicket();

        $this->_getValidator()->processHelpdeskTicketNew($ticket);
    }

    public function helpdeskSupportResponse($observer)
    {
        $ticket = $observer->getEvent()->getTicket();
        $comment = $observer->getEvent()->getComment();

        $this->_getValidator()->processHelpdeskSupportResponse($ticket, $comment);
    }

    protected function _processQuoteSuccessLowStock()
    {
        if (empty($this->itemsQty)) {
            return;
        }
        $stockItems = Mage::getResourceModel('cataloginventory/stock_item_collection')
            ->addFieldToFilter('item_id', array('in' => array_keys($this->itemsQty)));

        $validator = $this->_getValidator();

        foreach ($stockItems as $item) {
            $item->setOrigData('qty', $this->itemsQty[$item->getId()]);
            $validator->processStockItem($item);
        }
    }

    protected function _processQuoteSuccessOrderedQtyExcced($order)
    {
        $validator = $this->_getValidator();

        foreach ($order->getAllItems() as $item) {
            $stockItem = $item->getProduct()->getStockItem(); /* @var Mage_CatalogInventory_Model_Stock_Item $stockItem */
            if (!$stockItem->getManageStock()) {
                continue;
            }
            $validator->processOrderedItem($item);
        }
    }

    protected function _processCreateOrderRequiringApproval($order)
    {
        if (!$order->getIsApproved()) {
            $this->_getValidator()->processCreateOrderRequiringApproval($order);
        }
    }

    /**
     * @return Blackbox_Notification_Model_Validator
     */
    protected function _getValidator()
    {
        return Mage::getSingleton('blackbox_notification/validator');
    }
}