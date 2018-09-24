<?php

/**
 * Order Approval Rule data model
 *
 * @method Blackbox_OrderApproval_Model_Resource_Rule _getResource()
 * @method Blackbox_OrderApproval_Model_Resource_Rule getResource()
 * @method string getName()
 * @method Blackbox_OrderApproval_Model_Rule setName(string $value)
 * @method string getDescription()
 * @method Blackbox_OrderApproval_Model_Rule setDescription(string $value)
 * @method string getFromDate()
 * @method Blackbox_OrderApproval_Model_Rule setFromDate(string $value)
 * @method string getToDate()
 * @method Blackbox_OrderApproval_Model_Rule setToDate(string $value)
 * @method int getUsesPerCustomer()
 * @method Blackbox_OrderApproval_Model_Rule setUsesPerCustomer(int $value)
 * @method int getUsesPerCoupon()
 * @method Blackbox_OrderApproval_Model_Rule setUsesPerCoupon(int $value)
 * @method int getIsActive()
 * @method Blackbox_OrderApproval_Model_Rule setIsActive(int $value)
 * @method string getConditionsSerialized()
 * @method Blackbox_OrderApproval_Model_Rule setConditionsSerialized(string $value)
 * @method string getActionsSerialized()
 * @method Blackbox_OrderApproval_Model_Rule setActionsSerialized(string $value)
 * @method string getWebsiteIds()
 * @method Blackbox_OrderApproval_Model_Rule setWebsiteIds(string $value)
 *
 * @package     Blackbox_OrderAproval
 */
class Blackbox_OrderApproval_Model_Rule extends Mage_Rule_Model_Abstract
{
    /**
     * Free Shipping option "For matching items only"
     */
    const FREE_SHIPPING_ITEM    = 1;

    /**
     * Free Shipping option "For shipment with matching items"
     */
    const FREE_SHIPPING_ADDRESS = 2;

    /**
     * Coupon types
     */
    const COUPON_TYPE_NO_COUPON = 1;
    const COUPON_TYPE_SPECIFIC  = 2;
    const COUPON_TYPE_AUTO      = 3;

    /**
     * Rule type actions
     */
    const ACTION_DENY = 'deny';
    const ACTION_ALLOW = 'allow';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'order_approval_rule';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getRule() in this case
     *
     * @var string
     */
    protected $_eventObject = 'rule';

    /**
     * Contain sores labels
     *
     * @deprecated after 1.6.2.0
     *
     * @var array
     */
    protected $_labels = array();

    /**
     * Store already validated addresses and validation results
     *
     * @var array
     */
    protected $_validatedItems = array();

    /**
     * Set resource model and Id field name
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('order_approval/rule');
        $this->setIdFieldName('rule_id');
    }

    /**
     * Save/delete coupon
     *
     * @return Blackbox_OrderAproval_Model_Rule
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        return $this;
    }

    /**
     * Initialize rule model data from array.
     * Set store labels if applicable.
     *
     * @param array $data
     *
     * @return Blackbox_OrderAproval_Model_Rule
     */
    public function loadPost(array $data)
    {
        parent::loadPost($data);

        if (isset($data['store_labels'])) {
            $this->setStoreLabels($data['store_labels']);
        }

        return $this;
    }

    /**
     * Get rule condition combine model instance
     *
     * @return Blackbox_OrderApproval_Model_Rule_Condition_Customer_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('order_approval/rule_condition_customer_combine');
    }

    /**
     * Get rule condition combine model instance
     *
     * @return Blackbox_OrderApproval_Model_Rule_Condition_Order_Item_Found_Any
     */
    public function getActionsInstance()
    {
        return Mage::getModel('order_approval/rule_condition_order_item_found_any');
    }

    /**
     * Get Rule label by specified store
     *
     * @param Mage_Core_Model_Store|int|bool|null $store
     *
     * @return string|bool
     */
    public function getStoreLabel($store = null)
    {
        $storeId = Mage::app()->getStore($store)->getId();
        $labels = (array)$this->getStoreLabels();

        if (isset($labels[$storeId])) {
            return $labels[$storeId];
        } elseif (isset($labels[0]) && $labels[0]) {
            return $labels[0];
        }

        return false;
    }

    /**
     * Set if not yet and retrieve rule store labels
     *
     * @return array
     */
    public function getStoreLabels()
    {
        if (!$this->hasStoreLabels()) {
            $labels = $this->_getResource()->getStoreLabels($this->getId());
            $this->setStoreLabels($labels);
        }

        return $this->_getData('store_labels');
    }

    /**
     * Check cached validation result for specific address
     *
     * @param   Mage_Core_Model_Abstract $item
     * @return  bool
     */
    public function hasIsValidForItem(Mage_Core_Model_Abstract $item)
    {
        $itemId = $item->getId();
        return isset($this->_validatedItems[$itemId]) ? true : false;
    }

    /**
     * Set validation result for specific address to results cache
     *
     * @param   Mage_Core_Model_Abstract $item
     * @param   bool $validationResult
     * @return  Blackbox_OrderAproval_Model_Rule
     */
    public function setIsValidForItem(Mage_Core_Model_Abstract $item, $validationResult)
    {
        $itemId = $item->getId();
        $this->_validatedItems[$itemId] = $validationResult;
        return $this;
    }

    /**
     * Get cached validation result for specific address
     *
     * @param   Mage_Core_Model_Abstract $item
     * @return  bool
     */
    public function getIsValidForItem(Mage_Core_Model_Abstract $item)
    {
        $addressId = $item->getId();
        return isset($this->_validatedItems[$addressId]) ? $this->_validatedItems[$addressId] : false;
    }

    public function hasQtyCondition()
    {
        if ($this->hasActionsSerialized()) {
            return strpos($this->getActionsSerialized(), 'order_approval/rule_condition_order_item_max_approval_qty') !== false;
        } else if ($this->_actions) {
            return $this->_hasQtyConditionRecursive($this->getActions());
        }
        return false;
    }

    protected function _hasQtyConditionRecursive($conditions)
    {
        foreach ($conditions->getConditions() as $condition) {
            if ($condition instanceof Mage_Rule_Model_Condition_Combine) {
                if ($this->_hasQtyConditionRecursive($condition)) {
                    return true;
                }
            } else if ($condition instanceof Blackbox_OrderApproval_Model_Rule_Condition_Order_Item_Max_Approval_Qty) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns rule as an array for admin interface
     *
     * @deprecated after 1.6.2.0
     *
     * @param array $arrAttributes
     *
     * Output example:
     * array(
     *   'name'=>'Example rule',
     *   'conditions'=>{condition_combine::toArray}
     *   'actions'=>{action_collection::toArray}
     * )
     *
     * @return array
     */
    public function toArray(array $arrAttributes = array())
    {
        return parent::toArray($arrAttributes);
    }
}
