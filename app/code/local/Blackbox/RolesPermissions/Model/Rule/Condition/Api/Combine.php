<?php

class Blackbox_RolesPermissions_Model_Rule_Condition_Api_Combine extends Mage_Rule_Model_Condition_Combine
{
    protected $_apiResourcesInfo = null;

    const API_TYPE_RESOURCE = 'api_resource';

    /**
     * Initialize and retrieve a helper instance
     * @return Mage_Catalog_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('rolespermissions');
    }

    /**
     * Initialize a rule condition
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('rolespermissions/rule_condition_api_combine');
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
                    'label' => Mage::helper('catalog')->__('Access to api'),
                    'value' => 'rolespermissions/rule_condition_api_access'
                ),
                array(
                    'label' => Mage::helper('catalog')->__('ACL Resource'),
                    'value' => $this->_getAclConditions(self::API_TYPE_RESOURCE),
                ),
            )
        );
        return $conditions;
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml().
            Mage::helper('rule')->__('Select the items to which the rule will apply:');
        if ($this->getId() != '1') {
            $html.= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    /**
     * Validate a condition with the checking of the child value
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $this->setAggregator('any');
        $this->setValue('true');

        $valid = parent::validate($object);

        return $valid;
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
        if (!array_key_exists($conditionType, $this->_apiResourcesInfo)) {
            $this->_apiResourcesInfo[$conditionType] = array();
        }

        $conditionKey = sprintf('%s|%s', $conditionModel, $attributeCode);

        $this->_apiResourcesInfo[$conditionType][$conditionKey] = array(
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
    protected function _getAclConditions($conditionsGroup)
    {
        $this->_initializeapiResourcesInfo();
        return array_key_exists($conditionsGroup, $this->_apiResourcesInfo)
            ? $this->_apiResourcesInfo[$conditionsGroup]
            : array();
    }

    /**
     * Check whether the category attribute information exists and initialize it if missing
     * @return $this
     */
    protected function _initializeapiResourcesInfo()
    {
        if (is_null($this->_apiResourcesInfo)) {
            $this->_apiResourcesInfo = array();
            $resources = Mage::getResourceModel('api/acl')->loadAcl()->getResources();

            foreach ($resources as $resource) {
                $this->_addAttributeToConditionGroup(
                    self::API_TYPE_RESOURCE,
                    'rolespermissions/rule_condition_api',
                    $resource,
                    $resource
                );
            }
        }

        return $this;
    }
}
