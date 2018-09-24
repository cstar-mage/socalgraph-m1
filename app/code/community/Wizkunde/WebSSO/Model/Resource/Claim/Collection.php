<?php

/**
 * Class Wizkunde_WebSSO_Model_Resource_Claim_Collection
 */
class Wizkunde_WebSSO_Model_Resource_Claim_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection class for the claim resource
     */
    protected function _construct()
    {
        $this->_init('websso/claim');
    }
}