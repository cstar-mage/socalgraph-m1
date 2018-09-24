<?php

class Blackbox_RolesPermissions_Model_Rule_Condition_Cms_Page_Combine extends Mage_Rule_Model_Condition_Combine
{
    /**
     * "Cms page attribute match a value" conditions group
     */
    const CMS_PAGE_ATTRIBUTES_TYPE_CMS_PAGE = 'cms_page_attribute_match';

    /**
     * Cms page attributes info
     * @var array
     */
    protected $_cmsPageAttributesInfo = null;

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
        if (!array_key_exists($conditionType, $this->_cmsPageAttributesInfo)) {
            $this->_cmsPageAttributesInfo[$conditionType] = array();
        }

        $conditionKey = sprintf('%s|%s', $conditionModel, $attributeCode);

        $this->_cmsPageAttributesInfo[$conditionType][$conditionKey] = array(
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
        return array_key_exists($conditionsGroup, $this->_cmsPageAttributesInfo)
            ? $this->_cmsPageAttributesInfo[$conditionsGroup]
            : array();
    }

    /**
     * Check whether the cmsPage attribute information exists and initialize it if missing
     * @return $this
     */
    protected function _initializeCategoryAttributesInfo()
    {
        if (is_null($this->_cmsPageAttributesInfo)) {
            $this->_cmsPageAttributesInfo = array();
            $cmsPageAttributes = Mage::getModel('rolespermissions/rule_condition_cms_page')
                ->loadAttributeOptions()
                ->getAttributeOption();
            foreach ($cmsPageAttributes as $attributeCode => $attributeLabel) {
                if (!$attributeLabel) {
                    $attributeLabel = $attributeCode;
                }
                $this->_addAttributeToConditionGroup(
                    self::CMS_PAGE_ATTRIBUTES_TYPE_CMS_PAGE,
                    'rolespermissions/rule_condition_cms_page',
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
        $this->setType('rolespermissions/rule_condition_cms_page_combine');
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
                    'value' => 'rolespermissions/rule_condition_cms_page_combine'
                ),
                array(
                    'label' => Mage::helper('catalog')->__('Cms Page Attribute'),
                    'value' => $this->_getAttributeConditions(self::CMS_PAGE_ATTRIBUTES_TYPE_CMS_PAGE),
                )
            )
        );
        return $conditions;
    }

    /**
     * Collect all validated attributes
     * @param $cmsPageCollection
     *
     * @return $this
     */
    public function collectValidatedAttributes($cmsPageCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($cmsPageCollection);
        }
        return $this;
    }
}
