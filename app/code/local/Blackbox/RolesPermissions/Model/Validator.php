<?php

/**
 * RolesPermissions Validator Model
 *
 * Allows dispatching before and after events for each controller action
 *
 * @package    Blackbox_RolesPermissions
 */
class Blackbox_RolesPermissions_Model_Validator extends Mage_Core_Model_Abstract
{
    /**
     * Rule source collection
     *
     * @var Blackbox_RolesPermissions_Model_Resource_Rule_Collection[]
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
     * Defines if method Blackbox_RolesPermissions_Model_Validator::process() was already called
     * Used for clearing applied rule ids in Quote and in Address
     *
     * @deprecated since 1.4.2.0
     * @var bool
     */
    protected $_isFirstTimeProcessRun = false;

    /**
     * Defines if method Blackbox_RolesPermissions_Model_Validator::reset() wasn't called
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
     * @return  Blackbox_RolesPermissions_Model_Validator
     */
    public function init($websiteId, $scope, $customer = null)
    {
        $this->setWebsiteId($websiteId)
            ->setScope($scope);

        $key = $websiteId . '_' . $scope;
        if (!isset($this->_rules[$key])) {
            $this->_rules[$key] = Mage::getResourceModel('rolespermissions/rule_collection')
                ->setValidationFilter($websiteId, $scope)
                ->load();
        }

        if (!$customer && !($customer = $this->_getGlobalCustomer())) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                $customer->setGroupId(Mage::getSingleton('customer/session')->getCustomerGroupId());
            }
        }
        $this->setCustomer($customer);


        return $this;
    }

    /**
     * @return Mage_Customer_Model_Customer
     */
    protected function _getGlobalCustomer()
    {
        $customer = null;
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
        } else if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('admin/session')->getUser()->getCustomer();
        } else if (Mage::getSingleton('api/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('api/session')->getUser()->getCustomer();
        }

        return $customer;
    }

    /**
     * Get rules collection for current object state
     *
     * @return Blackbox_RolesPermissions_Model_Resource_Rule_Collection
     */
    protected function _getRules()
    {
        $key = $this->getWebsiteId() . '_' . $this->getScope();
        return $this->_rules[$key];
    }

    /**
     * Check if rule can be applied for specific address/quote/customer
     *
     * @param   Blackbox_RolesPermissions_Model_Rule $rule
     * @param   Mage_Core_Model_Abstract $item
     * @return  bool
     */
    protected function _canProcessRule($rule, $item)
    {
        if ($rule->getScope() != $this->getScope()) {
            return false;
        }

        if ($rule->hasIsValidForItem($item) && !$item->isObjectNew()) {
            return $rule->getIsValidForItem($item);
        }

        $rule->afterLoad();
        /**
         * quote does not meet rule's conditions
         */
        if (!$rule->validate($item)) {
            $rule->setIsValidForItem($item, false);
            return false;
        }
        /**
         * passed all validations, remember to be valid
         */
        $rule->setIsValidForItem($item, true);
        return true;
    }

    /**
     * Item permission calculation process
     *
     * @param   Mage_Core_Model_Abstract $item
     * @return  Blackbox_RolesPermissions_Model_Validator
     */
    public function process($item)
    {
        $this->_stopFurtherRules = false;

        $result = null;

        foreach ($this->_getRules() as $rule) {
            if (isset($priority) && (int)$rule->getSortOrder() < $priority) {
                continue;
            }

            /* @var $rule Blackbox_RolesPermissions_Model_Rule */
            if (!$this->_canProcessRule($rule, $this->getCustomer())) {
                continue;
            }

            if (!$rule->getActions()->validate($item)) {
                continue;
            }

            $result = $rule->getSimpleAction();
            $priority = (int)$rule->getSortOrder();

            Mage::dispatchEvent('rolespermissions_validator_process', array(
                'rule'    => $rule,
                'item'    => $item,
                'result'  => $result,
            ));

            if ($rule->getStopRulesProcessing()) {
                $this->_stopFurtherRules = true;
                break;
            }
        }

        $this->setResult($result);

        return $this;
    }

    public function isFound()
    {
        return $this->getResult() != null;
    }

    public function isAccessDenied()
    {
        return Blackbox_RolesPermissions_Model_Rule::ACTION_DENY == $this->getResult();
    }

    public function isAccessAllowed()
    {
        return Blackbox_RolesPermissions_Model_Rule::ACTION_ALLOW == $this->getResult();
    }

    /**
     * wrap Mage::getSingleton
     *
     * @param string $name
     * @return mixed
     */
    protected  function _getSingleton($name) {
        return Mage::getSingleton($name);
    }

    /**
     * wrap Mage::helper
     *
     * @param string $name
     * @return Mage_Weee_Helper_Data
     */
    protected function _getHelper($name) {
        return Mage::helper($name);
    }
}
