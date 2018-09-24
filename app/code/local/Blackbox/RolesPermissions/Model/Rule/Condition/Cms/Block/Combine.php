<?php

class Blackbox_RolesPermissions_Model_Rule_Condition_Cms_Block_Combine extends Mage_Rule_Model_Condition_Combine
{
    /**
     * "Cms block attribute match a value" conditions group
     */
    const CMS_BLOCK_ATTRIBUTES_TYPE_CMS_BLOCK = 'cms_block_attribute_match';

    /**
     * Cms block attributes info
     * @var array
     */
    protected $_cmsBlockAttributesInfo = null;

    /**
     * Initialize and retrieve a helper instance
     * @return Mage_Cms_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('cms');
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
     * Retrieve a conditions by group_id
     * @param $conditionsGroup
     *
     * @return array
     */
    protected function _getAttributeConditions($conditionsGroup)
    {
        $this->_initializeCategoryAttributesInfo();
        return array_key_exists($conditionsGroup, $this->_cmsBlockAttributesInfo)
            ? $this->_cmsBlockAttributesInfo[$conditionsGroup]
            : array();
    }

    /**
     * Check whether the cmsBlock attribute information exists and initialize it if missing
     * @return $this
     */
    protected function _initializeCategoryAttributesInfo()
    {
        if (is_null($this->_cmsBlockAttributesInfo)) {
            $this->_cmsBlockAttributesInfo = array();
            $cmsBlockAttributes = Mage::getModel('rolespermissions/rule_condition_cms_block')
                ->loadAttributeOptions()
                ->getAttributeOption();
            foreach ($cmsBlockAttributes as $attributeCode => $attributeLabel) {
                if (!$attributeLabel) {
                    $attributeLabel = $attributeCode;
                }
                $this->_addAttributeToConditionGroup(
                    self::CMS_BLOCK_ATTRIBUTES_TYPE_CMS_BLOCK,
                    'rolespermissions/rule_condition_cms_block',
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
        $this->setType('rolespermissions/rule_condition_cms_block_combine');
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
                    'value' => 'rolespermissions/rule_condition_cms_block_combine'
                ),
                array(
                    'label' => Mage::helper('catalog')->__('Cms Block Attribute'),
                    'value' => $this->_getAttributeConditions(self::CMS_BLOCK_ATTRIBUTES_TYPE_CMS_BLOCK),
                )
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
