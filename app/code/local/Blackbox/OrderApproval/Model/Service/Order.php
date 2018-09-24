<?php

class Blackbox_OrderApproval_Model_Service_Order extends Mage_Sales_Model_Service_Order
{
    /**
     * Prepare order approval based on order data and requested items qtys. If $qtys is not empty - the function will
     * prepare only specified items, otherwise all containing in the order.
     *
     * @param array $qtys
     * @param Blackbox_OrderApproval_Model_Rule $rule
     * @return Blackbox_OrderApproval_Model_Approval
     */
    public function prepareApproval($qtys = array(), $rule, $user)
    {
        $this->updateLocaleNumbers($qtys);
        $approval = $this->_convertor->toApproval($this->_order, $rule, $user);
        $totalQty = 0;
        foreach ($this->_order->getAllItems() as $orderItem) {
            $orderItem->setApproveRuleId($rule->getId());
            if (!$this->_canApproveItem($orderItem, array(), $rule)) {
                $orderItem->setApproveRuleId(null);
                continue;
            }
            $item = $this->_convertor->itemToApprovalItem($orderItem);
            if ($orderItem->isDummy()) {
                $qty = $orderItem->getQtyOrdered() ? $orderItem->getQtyOrdered() : 1;
            } else {
                if (isset($qtys[$orderItem->getId()])) {
                    $qty = (float) $qtys[$orderItem->getId()];
                } elseif (!count($qtys)) {
                    $qty = $orderItem->getQtyToApprove();
                } else {
                    $qty = 0;
                }
            }

            $totalQty += $qty;
            $item->setQty($qty);
            $approval->addItem($item);
            $orderItem->setApproveRuleId(null);
        }

        $approval->setTotalQty($totalQty);
        $this->_order->getApprovalCollection()->addItem($approval);

        return $approval;
    }

    /**
     * Check if order item can be approved. Dummy item can be approval or with his childrens or
     * with parent item which is included to approval
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @param array $qtys
     * @return bool
     */
    protected function _canApproveItemQty($item, $qtys=array())
    {
        $this->updateLocaleNumbers($qtys);

        if ($item->isDummy()) {
            if ($item->getHasChildren()) {
                foreach ($item->getChildrenItems() as $child) {
                    if (empty($qtys)) {
                        if ($child->getQtyToApprove() > 0) {
                            return true;
                        }
                    } else {
                        if (isset($qtys[$child->getId()]) && $qtys[$child->getId()] > 0) {
                            return true;
                        }
                    }
                }
                return false;
            } else if($item->getParentItem()) {
                $parent = $item->getParentItem();
                if (empty($qtys)) {
                    return $parent->getQtyToApprove() > 0;
                } else {
                    return isset($qtys[$parent->getId()]) && $qtys[$parent->getId()] > 0;
                }
            }
        } else {
            return $item->getQtyToApprove() > 0;
        }
    }

    /**
     * Prepare order invoice based on order data and requested items qtys. If $qtys is not empty - the function will
     * prepare only specified items, otherwise all containing in the order.
     *
     * @param array $qtys
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function prepareInvoice($qtys = array())
    {
        $this->updateLocaleNumbers($qtys);
        $invoice = $this->_convertor->toInvoice($this->_order);
        $totalQty = 0;
        foreach ($this->_order->getAllItems() as $orderItem) {
            if (!$this->_canInvoiceItem($orderItem, array())) {
                continue;
            }
            $item = $this->_convertor->itemToInvoiceItem($orderItem);
            if ($orderItem->isDummy()) {
                $qty = ($orderItem->getQtyOrdered() ? $orderItem->getQtyOrdered() : 1) - $orderItem->getQtyToApprove();
            } else {
                if (isset($qtys[$orderItem->getId()])) {
                    $qty = (float) $qtys[$orderItem->getId()];
                    $qty = min($qty, $orderItem->getQtyToInvoice() - $orderItem->getQtyToApprove());
                } elseif (!count($qtys)) {
                    $qty = $orderItem->getQtyToInvoice() - $orderItem->getQtyToApprove();
                } else {
                    $qty = 0;
                }
            }

            $totalQty += $qty;
            $item->setQty($qty);
            $invoice->addItem($item);
        }

        $invoice->setTotalQty($totalQty);
        $invoice->collectTotals();
        $this->_order->getInvoiceCollection()->addItem($invoice);

        return $invoice;
    }

    protected function _canApproveItem($item, $qtys=array(), $rule)
    {
        return $this->_canApproveItemQty($item, $qtys) && Mage::helper('order_approval')->canApproveItem($item, $rule);
    }

    protected function _canInvoiceItem($item, $qtys = array())
    {
        if ($item->getLockedDoInvoice()) {
            return false;
        }
        $this->updateLocaleNumbers($qtys);

        if ($item->isDummy()) {
            if ($item->getHasChildren()) {
                foreach ($item->getChildrenItems() as $child) {
                    if (empty($qtys)) {
                        if ($child->getQtyToInvoice() - $child->getQtyToApprove() > 0) {
                            return true;
                        }
                    } else {
                        if (isset($qtys[$child->getId()]) && $qtys[$child->getId()] - $child->getQtyToApprove() > 0) {
                            return true;
                        }
                    }
                }
                return false;
            } else if($item->getParentItem()) {
                $parent = $item->getParentItem();
                if (empty($qtys)) {
                    return $parent->getQtyToInvoice() - $parent->getQtyToApprove() > 0;
                } else {
                    return isset($qtys[$parent->getId()]) && $qtys[$parent->getId()] - $parent->getQtyToApprove() > 0;
                }
            }
        } else {
            return $item->getQtyToInvoice() - $item->getQtyToApprove() > 0;
        }
    }

    /**
     * Prepare order shipment based on order items and requested items qty
     *
     * @param array $qtys
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function prepareShipment($qtys = array(), $invoice = null)
    {
        $this->updateLocaleNumbers($qtys);
        $totalQty = 0;
        $shipment = $this->_convertor->toShipment($this->_order);
        if ($invoice) {
            $shipment->setInvoiceId($invoice->getId());
            $this->_collectInvoiceOrderItemIds($invoice);
        } else {
            $this->_invoicedOrderItemIds = null;
        }
        foreach ($this->_order->getAllItems() as $orderItem) {
            if (!$this->_canShipItem($orderItem, $qtys)) {
                continue;
            }

            $item = $this->_convertor->itemToShipmentItem($orderItem);

            if ($orderItem->isDummy(true)) {
                $qty = 0;
                if (isset($qtys[$orderItem->getParentItemId()])) {
                    $productOptions = $orderItem->getProductOptions();
                    if (isset($productOptions['bundle_selection_attributes'])) {
                        $bundleSelectionAttributes = unserialize($productOptions['bundle_selection_attributes']);

                        if ($bundleSelectionAttributes) {
                            $qty = $bundleSelectionAttributes['qty'] * $qtys[$orderItem->getParentItemId()];
                            $qty = min($qty, $orderItem->getSimpleQtyToShip());
                            $qty = min($qty, $this->_getItemShipMaxQty($orderItem));

                            $item->setQty($qty);
                            $shipment->addItem($item);
                            continue;
                        } else {
                            $qty = 1;
                        }
                    }
                } else {
                    $qty = 1;
                }
            } else {
                if (isset($qtys[$orderItem->getId()])) {
                    $qty = min($qtys[$orderItem->getId()], $orderItem->getQtyToShip());
                } elseif (!count($qtys)) {
                    $qty = $orderItem->getQtyToShip();
                } else {
                    continue;
                }
            }

            $qty = min($qty, $this->_getItemShipMaxQty($orderItem));

            $totalQty += $qty;
            $item->setQty($qty);
            $shipment->addItem($item);
        }

        $shipment->setTotalQty($totalQty);
        return $shipment;
    }

    protected function _canShipItem($item, $qtys = array())
    {
        if ($item->getIsVirtual() || $item->getLockedDoShip() || !$this->_isItemInInvoice($item)) {
            return false;
        }
        $this->updateLocaleNumbers($qtys);

        if ($item->isDummy(true)) {
            if ($item->getHasChildren()) {
                if ($item->isShipSeparately()) {
                    return true;
                }
                foreach ($item->getChildrenItems() as $child) {
                    if ($child->getIsVirtual()) {
                        continue;
                    }
                    if (empty($qtys)) {
                        if ($child->getQtyToShip() - $item->getQtyToApprove() > 0) {
                            return true;
                        }
                    } else {
                        if (isset($qtys[$child->getId()]) && $qtys[$child->getId()] - $item->getQtyToApprove() > 0) {
                            return true;
                        }
                    }
                }
                return false;
            } else if($item->getParentItem()) {
                $parent = $item->getParentItem();
                if (empty($qtys)) {
                    return $parent->getQtyToShip() - $item->getQtyToApprove() > 0;
                } else {
                    return isset($qtys[$parent->getId()]) && $qtys[$parent->getId()] - $item->getQtyToApprove() > 0;
                }
            }
        } else {
            return $item->getQtyToShip() - $item->getQtyToApprove() > 0;
        }
    }

    protected $_invoicedOrderItemIds = null;
    protected function _collectInvoiceOrderItemIds($invoice)
    {
        foreach ($invoice->getAllItems() as $item) {
            $this->_invoicedOrderItemIds[$item->getOrderItemId()] = $item->getQty();
        }
    }

    protected function _isItemInInvoice($item)
    {
        return $this->_invoicedOrderItemIds === null || isset($this->_invoicedOrderItemIds[$item->getId()]);
    }

    protected function _getItemShipMaxQty($item)
    {
        $qty = $item->getQtyApproved() - $item->getQtyShipped();
        if ($this->_invoicedOrderItemIds === null) {
            return $qty;
        } else {
            return min($this->_invoicedOrderItemIds[$item->getId()], $qty);
        }
    }

    /**
     * Prepare order disapproval based on order data.
     *
     * @param Blackbox_OrderApproval_Model_Rule $rule
     * @param Mage_Customer_Model_Customer $user
     * @return Blackbox_OrderApproval_Model_Approval
     */
    public function prepareDisapproval($rule, $user)
    {
        $approval = $this->_convertor->toDisapproval($this->_order, $rule, $user);

        $this->_order->getDisapprovalCollection()->addItem($approval);

        return $approval;
    }
}