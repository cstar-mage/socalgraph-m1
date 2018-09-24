<?php

/**
 * Order Approval Validator Model
 *
 * Allows dispatching before and after events for each controller action
 *
 * @package    Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Model_Validator extends Mage_Core_Model_Abstract
{
    /**
     * Rule source collection
     *
     * @var Blackbox_OrderApproval_Model_Resource_Rule_Collection
     */
    protected $_rules;

    /**
     * Rounding deltas
     *
     * @var array
     */
    protected $_roundingDeltas = array();

    /**
     * Base rounding deltas
     *
     * @var array
     */
    protected $_baseRoundingDeltas = array();

    /**
     * Quote address
     *
     * @var null|Mage_Sales_Model_Quote_Address
     */
    protected $_address = null;

    /**
     * Defines if method Blackbox_OrderApproval_Model_Validator::process() was already called
     * Used for clearing applied rule ids in Quote and in Address
     *
     * @deprecated since 1.4.2.0
     * @var bool
     */
    protected $_isFirstTimeProcessRun = false;

    /**
     * Defines if method Blackbox_OrderApproval_Model_Validator::reset() wasn't called
     * Used for clearing applied rule ids in Quote and in Address
     *
     * @var bool
     */
    protected $_isFirstTimeResetRun = true;

    /**
     * Information about item totals for rules.
     * @var array
     */
    protected $_rulesItemTotals = array();

    /**
     * Store information about addresses which cart fixed rule applied for
     *
     * @var array
     */
    protected $_cartFixedRuleUsedForAddress = array();

    /**
     * Defines if rule with stop further rules is already applied
     *
     * @var bool
     */
    protected $_stopFurtherRules = false;

    /**
     * Init validator
     * Init process load collection of rules for specific website,
     * customer group and coupon code
     *
     * @param   int $websiteId
     * @param   string $scope
     * @return  Blackbox_OrderApproval_Model_Validator
     */
    public function init($websiteId)
    {
        $this->setWebsiteId($websiteId);

        $key = $websiteId;
        if (!isset($this->_rules[$key])) {
            $this->_rules[$key] = Mage::getResourceModel('order_approval/rule_collection')
                ->setValidationFilter($websiteId)
                ->load();
        }


        return $this;
    }

    /**
     * Get rules collection for current object state
     *
     * @return Blackbox_OrderApproval_Model_Resource_Rule_Collection
     */
    protected function _getRules()
    {
        $key = $this->getWebsiteId();
        return $this->_rules[$key];
    }

    /**
     * Says if order have rules by which it could be approved
     *
     * @param Mage_Sales_Model_Order|Mage_Sales_Model_Order_Item|Mage_Sales_Model_Quote $order
     * @return bool
     */
    public function validateOrder($order)
    {
        foreach ($this->_getRules() as $rule) {
            if ($rule->getActions()->validate($order)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Says if customer can approve the order
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    public function validateCustomer(Mage_Sales_Model_Order $order, Mage_Customer_Model_Customer $customer)
    {
        $result = true;
        foreach ($this->_getRules() as $rule) {
            if (!$rule->getActions()->validate($order)) {
                continue;
            }

            $result = false;

            if ($rule->getConditions()->validate($customer)) {
                return true;
            }
        }

        return $result;
    }

    /**
     * @param Mage_Sales_Model_Order_Item $item
     * @param Mage_Customer_Model_Customer|null $customer
     * @return bool|int
     * true — no need to approve
     * false — can't approve with current customer
     */
    public function getItemApproveRule(Mage_Sales_Model_Order_Item $item, Mage_Customer_Model_Customer $customer = null)
    {
        $result = true;
        foreach ($this->_getRules() as $rule) {
            if (!$rule->getActions()->validate($item)) {
                continue;
            }

            $result = false;

            if ($customer) {
                if ($rule->getConditions()->validate($customer)) {
                    return $rule->getId();
                }
            } else {
                return $rule->getId();
            }
        }

        return $result;
    }

    /**
     * Check whether the item can be approved by the rule
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @param Blackbox_OrderApproval_Model_Rule $rule
     * @param Mage_Customer_Model_Customer|null $customer
     * @return bool
     */
    public function canApproveItemByRule(Mage_Sales_Model_Order_Item $item, Blackbox_OrderApproval_Model_Rule $rule, Mage_Customer_Model_Customer $customer = null)
    {
        $result = $rule->getActions()->validate($item);

        if ($result && $customer) {
            $result = $rule->getConditions()->validate($customer);
        }

        return $result;
    }

    /**
     * Check whether the order has not approved items that customer can approve
     *
     * @param $order
     * @param $customer
     * @return bool
     */
    public function getCanApprove($order, $customer) {
        return !empty($this->getItemsToApprove($order, $customer));
    }

    /**
     * Check whether the order is already approved
     *
     * @param $order
     * @return bool
     */
    public function isApproved($order)
    {
        return empty($this->getItemsToApprove($order));
    }

    /**
     * Returns all rules that can be applied to this order
     *
     * @param $order
     * @param null $customer
     * @return array
     */
    public function getAllRules($order, $customer = null)
    {
        $result = array();
        foreach ($this->_getRules() as $rule) {
            if (!$rule->getActions()->validate($order)) {
                continue;
            }

            if (!$customer || $rule->getConditions()->validate($customer)) {
                $result[] = $rule;
            }
        }

        return $result;
    }

    /**
     * Returns rules by which can be approved this order based on items that have not approved yet
     *
     * @param $order
     * @param null $customer
     * @return array
     */
    public function getAvailableRules($order, $customer = null)
    {
        $result = array();

        $itemsByRules = $this->getItemsToApproveByRule($order, $customer);

        foreach($itemsByRules as $itemsByRule) {
            $result[] = $itemsByRule['rule'];
        }

        return $result;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @param Mage_Customer_Model_Customer $customer
     */
    public function hasItemsCanInvoice($order, $customer)
    {
        foreach ($order->getAllItems() as $item) {
            /* @var Mage_Sales_Model_Order_Item $item */
            if ($item->getQtyToInvoice() > $item->getQtyToApprove()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns array of items that match existing rules
     *
     * @param $order
     * @param null $customer
     * @return array
     */
    protected function getItems($order, $customer = null)
    {
        $result = array();
        foreach ($this->_getRules() as $rule) {
            if ($customer && !$rule->getConditions()->validate($customer)) {
                continue;
            }

            if (empty($matchedItems = $rule->getActions()->getMatchedItemsQty($order))) {
                continue;
            }

            $result = $result + $matchedItems;
        }

        return $result;
    }

    /**
     * Returns array of items that need to be approved in the order and have not approved yet
     *
     * @param $order
     * @param null $customer
     * @return array
     */
    protected function getItemsToApprove($order, $customer = null)
    {
        $result = $this->getItems($order, $customer);

        foreach ($order->getAllItems() as $item) {
            if (isset($result[$item->getId()]) && (int)$item->getQtyApproved() == (int)$result[$item->getId()]) {
                unset($result[$item->getId()]);
            }
        }

        return $result;
    }

    /**
     * Returns items that need to be approved and have not approved yet, grouped by rule
     *
     * @param $order
     * @param null $customer
     * @return array
     */
    protected function getItemsToApproveByRule($order, $customer = null)
    {
        $result = array();
        foreach ($this->_getRules() as $rule) {
            if ($customer && !$rule->getConditions()->validate($customer)) {
                continue;
            }

            if (empty($itemsToApprove = $rule->getActions()->getMatchedItemsQty($order))) {
                continue;
            }

            foreach ($order->getAllItems() as $item) {
                if (array_key_exists($item->getId(), $itemsToApprove) && (int)$item->getQtyApprovedByRule($rule->getId()) == (int)$itemsToApprove[$item->getId()]) {
                    unset($itemsToApprove[$item->getId()]);
                }
            }

            if (!empty($itemsToApprove)) {
                $result[$rule->getId()] = array('rule' => $rule, 'items' => $itemsToApprove);
            }
        }

        return $result;
    }

    /**
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function needQtyApprove($quote)
    {
        /** @var Blackbox_OrderApproval_Model_Rule $rule */
        foreach ($this->_getRules() as $rule)
        {
            if ($rule->hasQtyCondition()) {
                if ($rule->getActions()->validate($quote)) {
                    return true;
                }
            }
        }
        return false;
    }
}
