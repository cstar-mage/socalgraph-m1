<?php

class Blackbox_Notification_Model_Resource_Notification_Rule_Collection extends Blackbox_Notification_Model_Resource_Rule_Collection_Abstract
{
    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = array(
        'website' => array(
            'associations_table' => 'blackbox_notification/notification_website',
            'rule_id_field'      => 'rule_id',
            'entity_id_field'    => 'website_id'
        )
    );

    /**
     * Set resource model and determine field mapping
     */
    protected function _construct()
    {
        $this->_init('blackbox_notification/notification_rule');
        $this->_map['fields']['rule_id'] = 'main_table.rule_id';
    }
}