<?php

class Blackbox_Notification_Model_Resource_Notification extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('blackbox_notification/notification', 'notification_id');
    }

    public function markAsViewed($customerId)
    {
        return $this->_getWriteAdapter()->update($this->getMainTable(), array('viewed' => 1), 'customer_id = ' . $customerId);
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

        return parent::_prepareDataForSave($object);
    }
}