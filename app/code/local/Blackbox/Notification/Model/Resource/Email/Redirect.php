<?php

class Blackbox_Notification_Model_Resource_Email_Redirect extends Mage_Core_Model_Resource_Db_Abstract
{
    const CLEANUP_COUNTER_PATH = 'blackbox_notification/email_redirect_url/cleanup_counter';

    protected function _construct()
    {
        $this->_init('blackbox_notification/email_redirect', 'id');
    }

    public function getConfig(Blackbox_Notification_Model_Email_Redirect $object)
    {
        $connection = $this->getReadConnection();
        $select = $connection->select()
            ->from(array('u' => $this->getTable('blackbox_notification/email_redirect_url')), 'u.*')
            ->joinInner(array('m' => $this->getTable('blackbox_notification/email_redirect_url_map')), "u.id = m.url_id AND m.redirect_id = {$object->getId()}", array('group_id'));

        $data = $connection->fetchAll($select);

        $result = array();

        foreach ($data as $item) {
            $result[$item['group_id']] = $item['url'];
        }

        return $result;
    }

    protected function getMap(Blackbox_Notification_Model_Email_Redirect $object)
    {
        $connection = $this->getReadConnection();
        $select = $connection->select()
            ->from($this->getTable('blackbox_notification/email_redirect_url_map'), '*')
            ->where("redirect_id = {$object->getId()}");

        $data = $connection->fetchAll($select);

        $result = array();

        foreach ($data as $item) {
            $result[$item['group_id']] = $item['url_id'];
        }

        return $result;
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $currentTime = Varien_Date::now();
        if ((!$object->getId() || $object->isObjectNew()) && !$object->getCreatedAt()) {
            $object->setCreatedAt($currentTime);
        }
        return parent::_beforeSave($object);
    }

    /**
     * @param Blackbox_Notification_Model_Email_Redirect $object
     * @return $this
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $connection = $this->getReadConnection();
        $config = $object->getConfig();

        if (empty($config)) {
            return $this;
        }

        $select = $connection->select()->from(array('u' => $this->getTable('blackbox_notification/email_redirect_url')))
            //->where('url IN (' . implode(',', array_map(function($item) use ($connection) { return $connection->quoteInto($item); }, $config)) . ')');
            ->where('url IN (?)', $config);
        $data = $connection->fetchAll($select);

        foreach ($config as $groupId => &$url) {
            $item = false;
            foreach ($data as $_item) {
                if ($_item['url'] == $url) {
                    $item = $_item;
                }
            }

            if ($item) {
                $url = $item['id'];
            } else {
                $connection->insert($this->getTable('blackbox_notification/email_redirect_url'), array('url' => $url));
                $url = $this->_getWriteAdapter()->lastInsertId($this->getTable('blackbox_notification/email_redirect_url'));
            }
        }

        $oldConfig = $this->getMap($object);
        $insert = array();

        foreach ($config as $groupId => $urlId) {
            if (isset($oldConfig[$groupId])) {
                if ($oldConfig[$groupId] != $urlId) {
                    $connection->update($this->getTable('blackbox_notification/email_redirect_url_map'), array('url_id' => $config[$groupId]), "group_id = $groupId AND redirect_id = {$object->getId()}");
                }
                unset($oldConfig[$groupId]);
            } else {
                $value = array(
                    'redirect_id' => $object->getId(),
                    'url_id' => $urlId
                );
                if ($groupId !== '') {
                    $value['group_id'] = $groupId;
                } else {
                    $value['group_id'] = null;
                }
                $insert[] = $value;
            }
        }

        if (!empty($insert)) {
            $connection->insertMultiple($this->getTable('blackbox_notification/email_redirect_url_map'), $insert);
        }

        foreach ($oldConfig as $groupId => $urlId) {
            $entry = array(
                'redirect_id = ?' => $object->getId(),
                'url_id = ?' => $urlId,
            );
            if ($groupId === '') {
                $entry['group_id IS NULL'] = true;
            } else {
                $entry['group_id = ?'] = $groupId;
            }
            $connection->delete($this->getTable('blackbox_notification/email_redirect_url_map'), $entry);
        }

        $this->_cleanUpUrls();

        return $this;
    }

    protected function _cleanUpUrls()
    {
        $number = (int)Mage::getStoreConfig(self::CLEANUP_COUNTER_PATH);
        if ($number < 0) {
            Mage::getConfig()->saveConfig(self::CLEANUP_COUNTER_PATH, ++$number);
            return;
        }

        $connection = $this->getReadConnection();
        $select = $connection->select()->from(array('u' => $this->getTable('blackbox_notification/email_redirect_url')), 'id')
            ->joinLeft(array('m' => $this->getTable('blackbox_notification/email_redirect_url_map')), 'm.url_id = u.id' , '')
            ->where('m.redirect_id IS NULL');

        $delete = $connection->deleteFromSelect($select, 'u');
        $connection->query($delete);

        Mage::getConfig()->saveConfig('blackbox_notification/email_redirect_url/cleanup_counter', 0);
    }
}