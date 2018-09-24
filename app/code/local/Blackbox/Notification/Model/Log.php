<?php

/**
 * @method Blackbox_Notification_Model_Log setType(int $value)
 * @method int getType()
 * @method Blackbox_Notification_Model_Log setParams(array $value)
 * @method array getParams()
 * @method Blackbox_Notification_Model_Log setCreatedAt(string $value)
 * @method string getCreatedAt()
 *
 * Class Blackbox_Notification_Model_Log
 */
class Blackbox_Notification_Model_Log extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('blackbox_notification/log');
    }

    protected function _afterLoad()
    {
        if ($this->getParams()) {
            $this->setParams(json_decode($this->getParams(), true));
        }
        return parent::_afterLoad();
    }
}