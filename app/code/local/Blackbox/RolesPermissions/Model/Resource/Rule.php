<?php

/**
 * Sales Rule resource model
 *
 * @package Blackbox_RolesPermissions
 */
class Blackbox_RolesPermissions_Model_Resource_Rule extends Mage_Rule_Model_Resource_Abstract
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
     * Initialize main table and table id field
     */
    protected function _construct()
    {
        $this->_init('rolespermissions/rule', 'rule_id');
    }

    /**
     * Add customer group ids and website ids to rule data after load
     *
     * @param Mage_Core_Model_Abstract $object
     *
     * @return Blackbox_RolesPermissions_Model_Resource_Rule
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $object->setData('website_ids', (array)$this->getWebsiteIds($object->getId()));

        parent::_afterLoad($object);
        return $this;
    }

    /**
     * Bind sales rule to customer group(s) and website(s).
     * Save rule's associated store labels.
     * Save product attributes used in rule.
     *
     * @param Mage_Core_Model_Abstract $object
     *
     * @return Blackbox_RolesPermissions_Model_Resource_Rule
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->hasWebsiteIds()) {
            $websiteIds = $object->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = explode(',', (string)$websiteIds);
            }
            $this->bindRuleToEntity($object->getId(), $websiteIds, 'website');
        }

        return parent::_afterSave($object);
    }

    /**
     * Return codes of all product attributes currently used in promo rules for specified customer group and website
     *
     * @return mixed
     */
    public function getActiveAttributes()
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(array('a' => $this->getTable('rolespermissions/product_attribute')),
                new Zend_Db_Expr('DISTINCT ea.attribute_code'))
            ->joinInner(array('ea' => $this->getTable('eav/attribute')), 'ea.attribute_id = a.attribute_id', array());
        return $read->fetchAll($select);
    }
}
