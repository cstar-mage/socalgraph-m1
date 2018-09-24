<?php

/**
 * @method Blackbox_OrderApproval_Model_Sales_Order_Item setApproveInfoSerialized(string $value)
 * @method string getApproveInfoSerialized()
 * @method Blackbox_OrderApproval_Model_Sales_Order_Item setApproveRuleId(int $value)
 * @method int getApproveRuleId()
 *
 * Class Blackbox_OrderApproval_Model_Sales_Order_Item
 */
class Blackbox_OrderApproval_Model_Sales_Order_Item extends Mage_Sales_Model_Order_Item
{
    protected $_approveInfo = array();
    protected $_approveInfoInitialized = false;
    protected $_approveRuleIds = null;

    public function getQtyToApprove()
    {
        if ($this->getApproveRuleId()) {
            return $this->getQtyToApproveByRule($this->getApproveRuleId());
        }
        if ($this->getQtyOrdered() - $this->getQtyApproved() == 0 || Mage::helper('order_approval')->getItemApproveRule($this) === true) {
            return 0;
        }
        return $this->getQtyOrdered() - $this->getQtyApproved();
    }

    public function getQtyToApproveByRule($ruleId)
    {
        if ($this->getQtyOrdered() - $this->getQtyApprovedByRule($ruleId) == 0 || !Mage::helper('order_approval')->canApproveItemByRule($this, $ruleId, false)) {
            return 0;
        }
        return $this->getQtyOrdered() - $this->getQtyApprovedByRule($ruleId);
    }

    public function getQtyApproved()
    {
        if ($this->getApproveRuleId()) {
            return $this->getQtyApprovedByRule($this->getApproveRuleId());
        }
        return parent::getQtyApproved();
    }

    public function setQtyApproved($qty)
    {
        if ($this->getApproveRuleId()) {
            return $this->setQtyApprovedByRule($qty, $this->getApproveRuleId());
        }
        return parent::setQtyApproved($qty);
    }

    public function getQtyApprovedByRule($ruleId)
    {
        $this->_initApproveInfo();
        return $this->_approveInfo[$ruleId];
    }

    public function setQtyApprovedByRule($qty, $ruleId)
    {
        $this->_initApproveInfo();
        $this->_approveInfo[$ruleId] = $qty;
        return $this;
    }

    public function updateQtyApproved()
    {
        $ruleIds = $this->getApproveRuleIds();
        if (empty($ruleIds)) {
            return $this;
        }
        $this->_initApproveInfo();
        $minQty = PHP_INT_MAX;
        foreach($ruleIds as $ruleId) {
            $qty = (int)$this->_approveInfo[$ruleId];
            if ($qty < $minQty) {
                $minQty = $qty;
                if ($minQty == 0) {
                    break;
                }
            }
        }

        parent::setQtyApproved($minQty);
    }

    public function getApproveInfo()
    {
        $this->_initApproveInfo();
        return $this->_approveInfo;
    }

    public function setApproveInfo($info)
    {
        $this->_approveInfoInitialized = true;
        $this->_approveInfo = $info;
        return $this;
    }

    public function getApproveRuleIds()
    {
        if ($this->_approveRuleIds === null) {
            $validator = Mage::getSingleton('order_approval/validator'); /* @var Blackbox_OrderApproval_Model_Validator $validator*/
            $websiteId = Mage::getModel('core/store')->load($this->getStoreId())->getWebsiteId();
            $rules = $validator->init($websiteId)->getAllRules($this);

            $this->_approveRuleIds = array();
            foreach ($rules as $rule) {
                $this->_approveRuleIds[] = $rule->getId();
            }
        }
        return $this->_approveRuleIds;
    }

    protected function _beforeSave()
    {
        $this->setApproveInfoSerialized(serialize($this->getApproveInfo()));
        $this->updateQtyApproved();
        return parent::_beforeSave();
    }

    protected function _afterLoad()
    {
        $this->_initApproveInfo();
        return parent::_afterLoad();
    }

    /**
     * Deserializes approve info. _afterLoad not called from collection.
     * @return $this
     */
    protected function _initApproveInfo()
    {
        if (!$this->_approveInfoInitialized) {
            $this->_approveInfo = unserialize($this->getApproveInfoSerialized());
            $this->_approveInfoInitialized = true;
        }
        return $this;
    }
}