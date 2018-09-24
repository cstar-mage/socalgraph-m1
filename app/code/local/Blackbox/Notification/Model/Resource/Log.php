<?php

class Blackbox_Notification_Model_Resource_Log extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('blackbox_notification/log', 'notification_id');
    }

    /**
     * Prepare data for save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return array
     */
    protected function _prepareDataForSave(Mage_Core_Model_Abstract $object)
    {
        $currentTime = Varien_Date::now();
        if ((!$object->getId() || $object->isObjectNew()) && !$object->getCreatedAt()) {
            $object->setCreatedAt($currentTime);
        }

        if (is_array($object->getParams())) {
            $object->setParams(json_encode($object->getParams()));
        }

        return parent::_prepareDataForSave($object);
    }
}