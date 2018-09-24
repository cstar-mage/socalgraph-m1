<?php

/**
 * Model for Adminhtml to render the claim grid
 *
 * Class Wizkunde_WebSSO_Model_Claim
 */
class Wizkunde_WebSSO_Model_Claim extends Mage_Core_Model_Abstract
{
    /**
     * Constructor to init the claim resource
     */
    protected function _construct()
    {
        $this->_init('websso/claim');
    }
}