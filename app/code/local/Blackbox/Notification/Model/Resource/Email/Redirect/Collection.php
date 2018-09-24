<?php

class Blackbox_Notification_Model_Resource_Email_Redirect_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('blackbox_notification/email_redirect');
    }
}