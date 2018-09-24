<?php

/**
 * Permission Rule data model
 *
 * @method Blackbox_RolesPermissions_Model_Resource_Rule _getResource()
 * @method Blackbox_RolesPermissions_Model_Resource_Rule getResource()
 * @method string getName()
 * @method Blackbox_RolesPermissions_Model_Rule setName(string $value)
 * @method string getDescription()
 * @method Blackbox_RolesPermissions_Model_Rule setDescription(string $value)
 * @method string getFromDate()
 * @method Blackbox_RolesPermissions_Model_Rule setFromDate(string $value)
 * @method string getToDate()
 * @method Blackbox_RolesPermissions_Model_Rule setToDate(string $value)
 * @method int getUsesPerCustomer()
 * @method Blackbox_RolesPermissions_Model_Rule setUsesPerCustomer(int $value)
 * @method int getUsesPerCoupon()
 * @method Blackbox_RolesPermissions_Model_Rule setUsesPerCoupon(int $value)
 * @method int getIsActive()
 * @method Blackbox_RolesPermissions_Model_Rule setIsActive(int $value)
 * @method string getConditionsSerialized()
 * @method Blackbox_RolesPermissions_Model_Rule setConditionsSerialized(string $value)
 * @method string getActionsSerialized()
 * @method Blackbox_RolesPermissions_Model_Rule setActionsSerialized(string $value)
 * @method int getStopRulesProcessing()
 * @method Blackbox_RolesPermissions_Model_Rule setStopRulesProcessing(int $value)
 * @method int getIsAdvanced()
 * @method Blackbox_RolesPermissions_Model_Rule setIsAdvanced(int $value)
 * @method string getProductIds()
 * @method Blackbox_RolesPermissions_Model_Rule setProductIds(string $value)
 * @method int getSortOrder()
 * @method Blackbox_RolesPermissions_Model_Rule setSortOrder(int $value)
 * @method string getSimpleAction()
 * @method Blackbox_RolesPermissions_Model_Rule setSimpleAction(string $value)
 * @method float getDiscountAmount()
 * @method Blackbox_RolesPermissions_Model_Rule setDiscountAmount(float $value)
 * @method float getDiscountQty()
 * @method Blackbox_RolesPermissions_Model_Rule setDiscountQty(float $value)
 * @method int getDiscountStep()
 * @method Blackbox_RolesPermissions_Model_Rule setDiscountStep(int $value)
 * @method int getSimpleFreeShipping()
 * @method Blackbox_RolesPermissions_Model_Rule setSimpleFreeShipping(int $value)
 * @method int getApplyToShipping()
 * @method Blackbox_RolesPermissions_Model_Rule setApplyToShipping(int $value)
 * @method int getTimesUsed()
 * @method Blackbox_RolesPermissions_Model_Rule setTimesUsed(int $value)
 * @method int getIsRss()
 * @method Blackbox_RolesPermissions_Model_Rule setIsRss(int $value)
 * @method string getWebsiteIds()
 * @method Blackbox_RolesPermissions_Model_Rule setWebsiteIds(string $value)
 * @method int getCouponType()
 * @method Blackbox_RolesPermissions_Model_Rule setCouponType(int $value)
 * @method int getUseAutoGeneration()
 * @method Blackbox_RolesPermissions_Model_Rule setUseAutoGeneration(int $value)
 * @method string getCouponCode()
 * @method Blackbox_RolesPermissions_Model_Rule setCouponCode(string $value)
 * @method Blackbox_RolesPermissions_Model_Rule setScope(string $scope)
 *
 * @package     Blackbox_RolesPermissions
 */
class Blackbox_RolesPermissions_Model_Rule extends Mage_Rule_Model_Abstract
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
     * Rule scopes
     */
    const SCOPE_CMS_PAGES = 'cms_pages';
    const SCOPE_CMS_BLOCKS = 'cms_blocks';
    const SCOPE_CATEGORIES = 'categories';
    const SCOPE_PRODUCTS = 'products';
    const SCOPE_ADMIN = 'admin';
    const SCOPE_API = 'api';

    /**
     * Scope settings
     */
    protected $_scopes = array(
        self::SCOPE_CMS_PAGES => array(
            'action' => 'rolespermissions/rule_condition_cms_page_combine'
        ),
        self::SCOPE_CMS_BLOCKS => array(
            'action' => 'rolespermissions/rule_condition_cms_block_combine'
        ),
        self::SCOPE_CATEGORIES => array(
            'action' => 'rolespermissions/rule_condition_category_combine'
        ),
        self::SCOPE_PRODUCTS => array(
            'action' => 'rolespermissions/rule_condition_product_combine'
        ),
        self::SCOPE_ADMIN => array(
            'action' => 'rolespermissions/rule_condition_admin_combine'
        ),
        self::SCOPE_API => array(
            'action' => 'rolespermissions/rule_condition_api_combine'
        ),
    );

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'rolespermissions_rule';

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
        $this->_init('rolespermissions/rule');
        $this->setIdFieldName('rule_id');
    }

    /**
     * Save/delete coupon
     *
     * @return Blackbox_RolesPermissions_Model_Rule
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
     * @return Blackbox_RolesPermissions_Model_Rule
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
     * @return Blackbox_RolesPermissions_Model_Rule_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('rolespermissions/rule_condition_combine');
    }

    /**
     * Get rule condition combine model instance
     *
     * @return Blackbox_RolesPermissions_Model_Rule_Condition_Combine
     */
    public function getActionsInstance()
    {
        return Mage::getModel($this->_scopes[$this->getScope()]['action']);
    }

    /**
     * @return string
     */
    public function getScope()
    {
        $scope = parent::getScope();
        if (!$scope) {
            $this->setScope($scope = self::SCOPE_PRODUCTS);
        }
        return $scope;
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
     * @return  Blackbox_RolesPermissions_Model_Rule
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
