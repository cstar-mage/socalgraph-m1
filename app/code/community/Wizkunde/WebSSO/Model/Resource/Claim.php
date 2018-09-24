<?php

/**
 * Class Wizkunde_WebSSO_Model_Resource_Claim
 */
class Wizkunde_WebSSO_Model_Resource_Claim extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Constructor to init the claim resource
     */
    protected function _construct()
    {
        $this->_init('websso/claim', 'id');
    }
}