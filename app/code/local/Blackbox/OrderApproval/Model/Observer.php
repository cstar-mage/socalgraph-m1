<?php

class Blackbox_OrderApproval_Model_Observer
{
    public function orderEditRememberComment($observer)
    {
        $history = $observer->getEvent()->getHistory();
        if (!$history) {
            return;
        }

        $order = $observer->getEvent()->getOrder();
        $order->setEditCommentId($history->getId())->save();
    }

    public function setApprovedQtyForAutoapproved($observer)
    {
        $order = $observer->getEvent()->getOrder();

        if ($order->getIsApproved()) {
            return;
        }

        /** @var Blackbox_OrderApproval_Helper_Data $helper */
        $helper = Mage::helper('order_approval');
        $allApproved = true;
        foreach($order->getAllItems() as $item) {
            if ($item->getQtyOrdered() <= $item->getQtyApproved()) {
                continue;
            }
            if (!$helper->needApprove($item)) {
                $item->setQtyApproved($item->getQtyOrdered());
            } else {
                $allApproved = false;
            }
        }

        if ($allApproved) {
            $order->setIsApproved(1);
        }
    }
}