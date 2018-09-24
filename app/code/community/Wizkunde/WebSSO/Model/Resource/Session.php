<?php

/**
 * Class Wizkunde_WebSSO_Model_Resource_Session
 */
class Wizkunde_WebSSO_Model_Resource_Session extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Constructor to init the idp resource
     */
    protected function _construct()
    {
        $this->_init('websso/session', 'id');
    }
}