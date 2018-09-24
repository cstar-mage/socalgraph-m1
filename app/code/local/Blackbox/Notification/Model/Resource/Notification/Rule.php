<?php

class Blackbox_Notification_Model_Resource_Notification_Rule extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('blackbox_notification/notification_rule', 'rule_id');
    }
}