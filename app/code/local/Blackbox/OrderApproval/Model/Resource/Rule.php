<?php

/**
 * Order Approval rule resource model
 *
 * @package Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Model_Resource_Rule extends Mage_Rule_Model_Resource_Abstract
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
     * Initialize main table and table id field
     */
    protected function _construct()
    {
        $this->_init('order_approval/rule', 'rule_id');
    }

    /**
     * Add customer group ids and website ids to rule data after load
     *
     * @param Mage_Core_Model_Abstract $object
     *
     * @return Blackbox_OrderApproval_Model_Resource_Rule
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
     * @return Blackbox_OrderApproval_Model_Resource_Rule
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
}
