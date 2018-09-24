<?php

/**
 * Class Wizkunde_WebSSO_Model_Resource_Idp
 */
class Wizkunde_WebSSO_Model_Resource_Idp extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Constructor to init the idp resource
     */
    protected function _construct()
    {
        $this->_init('websso/idp', 'id');
    }
}