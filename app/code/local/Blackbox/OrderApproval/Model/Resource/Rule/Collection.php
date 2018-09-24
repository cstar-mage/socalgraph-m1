<?php

/**
 * Order Approval rule resource collection model
 *
 * @package     Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Model_Resource_Rule_Collection extends Mage_Rule_Model_Resource_Rule_Collection_Abstract
{
    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = array(
        'website' => array(
            'associations_table' => 'order_approval/website',
            'rule_id_field'      => 'rule_id',
            'entity_id_field'    => 'website_id'
        )
    );

    /**
     * Set resource model and determine field mapping
     */
    protected function _construct()
    {
        $this->_init('order_approval/rule');
        $this->_map['fields']['rule_id'] = 'main_table.rule_id';
    }

    /**
     * Filter collection by specified website.
     * Filter collection to use only active rules.
     *
     * @param int $websiteId
     *
     * @return Blackbox_OrderApproval_Model_Resource_Rule_Collection
     */
    public function setValidationFilter($websiteId)
    {
        if (!$this->getFlag('validation_filter')) {

            $this->getSelect()->reset();
            parent::_initSelect();

            $this->addWebsiteGroupFilter($websiteId);

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
     * @return Blackbox_OrderApproval_Model_Resource_Rule_Collection
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
     * @return Blackbox_OrderApproval_Model_Resource_Rule_Collection
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
