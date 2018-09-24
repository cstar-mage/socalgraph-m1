<?php

class Blackbox_LoginHistory_Model_Resource_Login_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('login_history/login');
    }
}