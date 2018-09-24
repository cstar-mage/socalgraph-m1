<?php

/**
 * Sales Rules resource collection model
 *
 * @package     Blackbox_RolesPermissions
 */
class Blackbox_RolesPermissions_Model_Resource_Rule_Collection extends Mage_Rule_Model_Resource_Rule_Collection_Abstract
{
    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = array(
        'website' => array(
            'associations_table' => 'rolespermissions/website',
            'rule_id_field'      => 'rule_id',
            'entity_id_field'    => 'website_id'
        )
    );

    /**
     * Set resource model and determine field mapping
     */
    protected function _construct()
    {
        $this->_init('rolespermissions/rule');
        $this->_map['fields']['rule_id'] = 'main_table.rule_id';
    }

    /**
     * Filter collection by specified website, scope.
     * Filter collection to use only active rules.
     * Involved sorting by sort_order column.
     *
     * @param int $websiteId
     * @param string $scope
     *
     * @return Blackbox_RolesPermissions_Model_Resource_Rule_Collection
     */
    public function setValidationFilter($websiteId, $scope)
    {
        if (!$this->getFlag('validation_filter')) {

            $this->getSelect()->reset();
            parent::_initSelect();

            $this->addWebsiteGroupFilter($websiteId);
            $this->addFieldToFilter('scope', $scope);

            $this->setOrder('sort_order', self::SORT_ORDER_DESC);
            $this->setFlag('validation_filter', true);
        }

        return $this;
    }

    /**
     * Filter collection by website(s).
     * Filter collection to only active rules.
     * Sorting is not involved
     *
     * @param int $websiteId
     *
     * @return Blackbox_RolesPermissions_Model_Resource_Rule_Collection
     */
    public function addWebsiteGroupFilter($websiteId)
    {
        if (!$this->getFlag('website_filter')) {

            $this->addWebsiteFilter($websiteId);
            $this->addIsActiveFilter();

            $this->setFlag('website_filter', true);
        }

        return $this;
    }

    /**
     * Find product attribute in conditions or actions
     *
     * @param string $attributeCode
     *
     * @return Blackbox_RolesPermissions_Model_Resource_Rule_Collection
     */
    public function addAttributeInConditionFilter($attributeCode)
    {
        $match = sprintf('%%%s%%', substr(serialize(array('attribute' => $attributeCode)), 5, -1));
        $field = $this->_getMappedField('conditions_serialized');
        $cCond = $this->_getConditionSql($field, array('like' => $match));
        $field = $this->_getMappedField('actions_serialized');
        $aCond = $this->_getConditionSql($field, array('like' => $match));

        $this->getSelect()->where(sprintf('(%s OR %s)', $cCond, $aCond), null, Varien_Db_Select::TYPE_CONDITION);

        return $this;
    }
}
