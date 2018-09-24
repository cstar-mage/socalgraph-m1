<?php

/**
 * Order Approval Item Model
 *
 * @method Blackbox_OrderApproval_Model_Resource_Approval_Item _getResource()
 * @method Blackbox_OrderApproval_Model_Resource_Approval_Item getResource()
 * @method int getParentId()
 * @method Blackbox_OrderApproval_Model_Approval_Item setParentId(int $value)
 * @method float getQty()
 * @method int getProductId()
 * @method Blackbox_OrderApproval_Model_Approval_Item setProductId(int $value)
 * @method int getOrderItemId()
 * @method Blackbox_OrderApproval_Model_Approval_Item setOrderItemId(int $value)
 * @method string getAdditionalData()
 * @method Blackbox_OrderApproval_Model_Approval_Item setAdditionalData(string $value)
 * @method string getDescription()
 * @method Blackbox_OrderApproval_Model_Approval_Item setDescription(string $value)
 * @method string getSku()
 * @method Blackbox_OrderApproval_Model_Approval_Item setSku(string $value)
 * @method string getName()
 * @method Blackbox_OrderApproval_Model_Approval_Item setName(string $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_OrderApproval_Model_Approval_Item extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'order_approval_item';
    protected $_eventObject = 'approval_item';

    protected $_approval = null;
    protected $_orderItem = null;

    /**
     * Initialize resource model
     */
    function _construct()
    {
        $this->_init('order_approval/approval_item');
    }

    /**
     * Declare approval instance
     *
     * @param   Blackbox_OrderApproval_Model_Approval $approval
     * @return  Blackbox_OrderApproval_Model_Approval_Item
     */
    public function setApproval(Blackbox_OrderApproval_Model_Approval $approval)
    {
        $this->_approval = $approval;
        return $this;
    }

    /**
     * Retrieve approval instance
     *
     * @return Blackbox_OrderApproval_Model_Approval
     */
    public function getApproval()
    {
        return $this->_approval;
    }

    /**
     * Declare order item instance
     *
     * @param   Mage_Sales_Model_Order_Item $item
     * @return  Blackbox_OrderApproval_Model_Approval_Item
     */
    public function setOrderItem(Mage_Sales_Model_Order_Item $item)
    {
        $this->_orderItem = $item;
        $this->setOrderItemId($item->getId());
        return $this;
    }

    /**
     * Retrieve order item instance
     *
     * @return Mage_Sales_Model_Order_Item
     */
    public function getOrderItem()
    {
        if (is_null($this->_orderItem)) {
            if ($this->getApproval()) {
                $this->_orderItem = $this->getApproval()->getOrder()->getItemById($this->getOrderItemId());
            }
            else {
                $this->_orderItem = Mage::getModel('sales/order_item')
                    ->load($this->getOrderItemId());
            }
        }
        return $this->_orderItem;
    }

    /**
     * Declare qty
     *
     * @param   float $qty
     * @return  Blackbox_OrderApproval_Model_Approval_Item
     */
    public function setQty($qty)
    {
        if ($this->getOrderItem()->getIsQtyDecimal()) {
            $qty = (float) $qty;
        }
        else {
            $qty = (int) $qty;
        }
        $qty = $qty > 0 ? $qty : 0;
        /**
         * Check qty availability
         */
        $qtyToApprove = sprintf("%F", $this->getOrderItem()->getQtyToApprove());
        $qty = sprintf("%F", $qty);
        if ($qty <= $qtyToApprove || $this->getOrderItem()->isDummy()) {
            $this->setData('qty', $qty);
        }
        else {
            Mage::throwException(
                Mage::helper('order_approval')->__('Invalid qty to approve item "%s"', $this->getName())
            );
        }
        return $this;
    }

    /**
     * Applying qty to order item
     *
     * @return Blackbox_OrderApproval_Model_Approval_Item
     */
    public function register()
    {
        $orderItem = $this->getOrderItem(); /* @var Blackbox_OrderApproval_Model_Sales_Order_Item $orderItem */
        $ruleId = $this->getApproval()->getRuleId();

        $orderItem->setQtyApprovedByRule($orderItem->getQtyApprovedByRule($ruleId) + $this->getQty(), $ruleId);
        $orderItem->updateQtyApproved();

        //$orderItem->setQtyApproved($orderItem->getQtyApproved()+$this->getQty());

        return $this;
    }

    /**
     * Cancelling approval item
     *
     * @return Blackbox_OrderApproval_Model_Approval_Item
     */
    public function cancel()
    {
        $orderItem = $this->getOrderItem(); /* @var Blackbox_OrderApproval_Model_Sales_Order_Item $orderItem */
        $ruleId = $this->getApproval()->getRuleId();

        $orderItem->setQtyApprovedByRule($orderItem->getQtyApprovedByRule($ruleId) - $this->getQty(), $ruleId);
        $orderItem->updateQtyApproved();

        //$orderItem->setQtyApproved($orderItem->getQtyApproved()-$this->getQty());

        return $this;
    }

    /**
     * Checking if the item is last
     *
     * @return bool
     */
    public function isLast()
    {
        if ((string)(float)$this->getQty() == (string)(float)$this->getOrderItem()->getQtyToApprove()) {
            return true;
        }
        return false;
    }

    /**
     * Before object save
     *
     * @return Blackbox_OrderApproval_Model_Approval_Item
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getApproval()) {
            $this->setParentId($this->getApproval()->getId());
        }

        return $this;
    }

    /**
     * After object save
     *
     * @return Blackbox_OrderApproval_Model_Approval_Item
     */
    protected function _afterSave()
    {
        if (null ==! $this->_orderItem) {
            $this->_orderItem->save();
        }

        parent::_afterSave();
        return $this;
    }
}
