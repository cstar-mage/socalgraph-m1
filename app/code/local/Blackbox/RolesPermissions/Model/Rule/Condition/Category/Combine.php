<?php

class Blackbox_RolesPermissions_Model_Rule_Condition_Category_Combine extends Mage_Rule_Model_Condition_Combine
{
    /**
     * "Category attribute match a value" conditions group
     */
    const CATEGORY_ATTRIBUTES_TYPE_CATEGORY = 'category_attribute_match';

    /**
     * "Category attribute is set" conditions group
     */
    const CATEGORY_ATTRIBUTES_TYPE_ISSET = 'category_attribute_isset';

    /**
     * Categories attributes info
     * @var array
     */
    protected $_categoryAttributesInfo = null;

    /**
     * Initialize and retrieve a helper instance
     * @return Mage_Catalog_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('catalog');
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
        if (!array_key_exists($conditionType, $this->_categoryAttributesInfo)) {
            $this->_categoryAttributesInfo[$conditionType] = array();
        }

        $conditionKey = sprintf('%s|%s', $conditionModel, $attributeCode);

        $this->_categoryAttributesInfo[$conditionType][$conditionKey] = array(
            'label' => $attributeLabel,
            'value' => $conditionKey
        );

        return $this;
    }

    /**
     * Retrieve a conditions by group_id
     * @param $conditionsGroup
     *
     * @return array
     */
    protected function _getAttributeConditions($conditionsGroup)
    {
        $this->_initializeCategoryAttributesInfo();
        return array_key_exists($conditionsGroup, $this->_categoryAttributesInfo)
            ? $this->_categoryAttributesInfo[$conditionsGroup]
            : array();
    }

    /**
     * Check whether the category attribute information exists and initialize it if missing
     * @return $this
     */
    protected function _initializeCategoryAttributesInfo()
    {
        if (is_null($this->_categoryAttributesInfo)) {
            $this->_categoryAttributesInfo = array();
            $categoryAttributes = Mage::getModel('rolespermissions/rule_condition_category')
                ->loadAttributeOptions()
                ->getAttributeOption();
            foreach ($categoryAttributes as $attributeCode => $attributeLabel) {
                if (!$attributeLabel) {
                    $attributeLabel = $attributeCode;
                }
                $this->_addAttributeToConditionGroup(
                    self::CATEGORY_ATTRIBUTES_TYPE_CATEGORY,
                    'rolespermissions/rule_condition_category',
                    $attributeCode,
                    $attributeLabel
                )->_addAttributeToConditionGroup(
                    self::CATEGORY_ATTRIBUTES_TYPE_ISSET,
                    'rolespermissions/rule_condition_category_attribute_assigned',
                    $attributeCode,
                    $attributeLabel
                );
            }
        }

        return $this;
    }

    /**
     * Initialize a rule condition
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('rolespermissions/rule_condition_category_combine');
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
                    'label' => Mage::helper('catalog')->__('Conditions Combination'),
                    'value' => 'rolespermissions/rule_condition_category_combine'
                ),
                array(
                    'label' => Mage::helper('catalog')->__('Category Attribute'),
                    'value' => $this->_getAttributeConditions(self::CATEGORY_ATTRIBUTES_TYPE_CATEGORY),
                ),
                array(
                    'label' => $this->_getHelper()->__('Category Attribute Assigned'),
                    'value' => $this->_getAttributeConditions(self::CATEGORY_ATTRIBUTES_TYPE_ISSET)
                )
            )
        );
        return $conditions;
    }

    /**
     * Collect all validated attributes
     * @param $categoryCollection
     *
     * @return $this
     */
    public function collectValidatedAttributes($categoryCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($categoryCollection);
        }
        return $this;
    }
}
