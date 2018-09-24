<?php

class Blackbox_RolesPermissions_Model_Rule_Condition_Admin_Combine extends Mage_Rule_Model_Condition_Combine
{
    protected $_adminResourcesInfo = null;

    const ADMIN_TYPE_RESOURCE = 'admin_resource';

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
        $this->setType('rolespermissions/rule_condition_admin_combine');
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
                    'label' => Mage::helper('catalog')->__('Access to admin dashboard'),
                    'value' => 'rolespermissions/rule_condition_admin_dashboard'
                ),
                array(
                    'label' => Mage::helper('catalog')->__('View prices'),
                    'value' => 'rolespermissions/rule_condition_admin_price'
                ),
                array(
                    'label' => Mage::helper('catalog')->__('ACL Resource'),
                    'value' => $this->_getAclConditions(self::ADMIN_TYPE_RESOURCE),
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
        if (!array_key_exists($conditionType, $this->_adminResourcesInfo)) {
            $this->_adminResourcesInfo[$conditionType] = array();
        }

        $conditionKey = sprintf('%s|%s', $conditionModel, $attributeCode);

        $this->_adminResourcesInfo[$conditionType][$conditionKey] = array(
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
        $this->_initializeAdminResourcesInfo();
        return array_key_exists($conditionsGroup, $this->_adminResourcesInfo)
            ? $this->_adminResourcesInfo[$conditionsGroup]
            : array();
    }

    /**
     * Check whether the category attribute information exists and initialize it if missing
     * @return $this
     */
    protected function _initializeAdminResourcesInfo()
    {
        if (is_null($this->_adminResourcesInfo)) {
            $this->_adminResourcesInfo = array();
            $resources = Mage::getResourceModel('admin/acl')->loadAcl()->getResources();

            foreach ($resources as $resource) {
                $this->_addAttributeToConditionGroup(
                    self::ADMIN_TYPE_RESOURCE,
                    'rolespermissions/rule_condition_admin',
                    $resource,
                    $resource
                );
            }
        }

        return $this;
    }
}
