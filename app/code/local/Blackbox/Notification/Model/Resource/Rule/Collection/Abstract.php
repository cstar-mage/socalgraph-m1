<?php
/**
 * Created by PhpStorm.
 * User: cheer
 * Date: 04.07.2018
 * Time: 13:05
 */
abstract class Blackbox_Notification_Model_Resource_Rule_Collection_Abstract extends Mage_Rule_Model_Resource_Rule_Collection_Abstract
{


    /**
     * Filter collection by specified website.
     * Filter collection to use only active rules.
     *
     * @param int $websiteId
     *
     * @return Blackbox_Notification_Model_Resource_Rule_Collection
     */
    public function setValidationFilter($websiteId, $type)
    {
        if (!$this->getFlag('validation_filter')) {

            $this->getSelect()->reset();
            parent::_initSelect();

            $this->addWebsiteGroupFilter($websiteId);
            $this->addTypeFilter($type);

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
     * @return Blackbox_Notification_Model_Resource_Rule_Collection
     */
    public function addWebsiteGroupFilter($websiteId)
    {
        if (Mage::app()->isSingleStoreMode()) {
            return;
        }
        if (!$this->getFlag('website_filter')) {

            $this->addWebsiteFilter($websiteId);
            $this->addIsActiveFilter();

            $this->setFlag('website_filter', true);
        }

        return $this;
    }

    /**
     * @param $typeId
     * @return $this
     */
    public function addTypeFilter($type)
    {
        if (!$this->getFlag('type_filter')) {

            $this->addFieldToFilter('type', $type);

            $this->setFlag('type_filter', true);
        }

        return $this;
    }

    /**
     * Find product attribute in conditions or actions
     *
     * @param string $attributeCode
     *
     * @return Blackbox_Notification_Model_Resource_Rule_Collection
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