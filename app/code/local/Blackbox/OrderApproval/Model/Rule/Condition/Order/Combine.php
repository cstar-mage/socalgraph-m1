<?php

class Blackbox_OrderApproval_Model_Rule_Condition_Order_Combine extends Mage_Rule_Model_Condition_Combine
{
    /**
     * "Order item" conditions group
     */
    const ORDER_ATTRIBUTES_TYPE_ORDER_ITEM = 'order_attribute_order_item';

    /**
     * "Customer" conditions group
     */
    const ORDER_ATTRIBUTES_TYPE_CUSTOMER = 'order_attribute_customer';

    /**
     * Cms block attributes info
     * @var array
     */
    protected $_cmsBlockAttributesInfo = null;

    /**
     * Initialize and retrieve a helper instance
     * @return Blackbox_OrderApproval_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('order_approval');
    }

    /**
     * Add an attribute condition to the conditions group
     * @param $conditionType
     * @param $conditionModel
     * @param $attributeCode
     * @param $attributeLabel
     *
     * @return $this
     */
    protected function _addAttributeToConditionGroup($conditionType, $conditionModel, $attributeCode, $attributeLabel)
    {
        if (!array_key_exists($conditionType, $this->_cmsBlockAttributesInfo)) {
            $this->_cmsBlockAttributesInfo[$conditionType] = array();
        }

        $conditionKey = sprintf('%s|%s', $conditionModel, $attributeCode);

        $this->_cmsBlockAttributesInfo[$conditionType][$conditionKey] = array(
            'label' => $attributeLabel,
            'value' => $conditionKey
        );

        return $this;
    }

    /**
     * Initialize a rule condition
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('order_approval/rule_condition_order_combine');
    }

    /**
     * Generate a conditions data
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            array(
                array(
                    'label' => Mage::helper('catalog')->__('Customer Condition Combination'),
                    'value' => 'order_approval/rule_condition_customer_combine'
                ),
                array(
                    'label' => Mage::helper('catalog')->__('Order Item Condition Combination'),
                    'value' => 'order_approval/rule_condition_order_item_found'
                ),
            )
        );
        return $conditions;
    }

    /**
     * Collect all validated attributes
     * @param $cmsBlockCollection
     *
     * @return $this
     */
    public function collectValidatedAttributes($cmsBlockCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($cmsBlockCollection);
        }
        return $this;
    }
}
