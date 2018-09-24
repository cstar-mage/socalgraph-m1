<?php

class Blackbox_OrderApproval_Model_Convert_Order extends Mage_Sales_Model_Convert_Order
{
    /**
     * Convert order object to approval
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  Blackbox_OrderApproval_Model_Approval
     */
    public function toApproval(Mage_Sales_Model_Order $order, Blackbox_OrderApproval_Model_Rule $rule, Mage_Customer_Model_Customer $user)
    {
        $approval = Mage::getModel('order_approval/approval');
        $approval->setOrder($order)
            ->setStoreId($order->getStoreId())
            ->setCustomerId($order->getCustomerId())
            ->setRule($rule)
            ->setUser($user);

        Mage::helper('core')->copyFieldset('sales_convert_order', 'to_approval', $order, $approval);
        return $approval;
    }

    /**
     * Convert order item object to approval item
     *
     * @param   Mage_Sales_Model_Order_Item $item
     * @return  Blackbox_OrderApproval_Model_Approval_Item
     */
    public function itemToApprovalItem(Mage_Sales_Model_Order_Item $item)
    {
        $approvalItem = Mage::getModel('order_approval/approval_item');
        $approvalItem->setOrderItem($item)
            ->setProductId($item->getProductId());

        Mage::helper('core')->copyFieldset('sales_convert_order_item', 'to_approval_item', $item, $approvalItem);
        return $approvalItem;
    }

    /**
     * Convert order object to disapproval
     *
     * @param   Mage_Sales_Model_Order $order
     * @param   Blackbox_OrderApproval_Model_Rule|null $rule
     * @param   Mage_Customer_Model_Customer $user
     * @return  Blackbox_OrderApproval_Model_Disapproval
     */
    public function toDisapproval(Mage_Sales_Model_Order $order, $rule, Mage_Customer_Model_Customer $user)
    {
        $disapproval = Mage::getModel('order_approval/disapproval');
        $disapproval->setOrder($order)
            ->setCustomerId($order->getCustomerId())
            ->setUser($user);
        if ($rule) {
            $disapproval->setRule($rule);
        }

        //Mage::helper('core')->copyFieldset('sales_convert_order', 'to_disapproval', $order, $disapproval);
        return $disapproval;
    }
}