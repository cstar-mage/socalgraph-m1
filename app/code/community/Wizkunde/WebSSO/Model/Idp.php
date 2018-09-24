<?php

/**
 * Model for Adminhtml to render the idp grid
 *
 * Class Wizkunde_WebSSO_Model_Idp
 */
class Wizkunde_WebSSO_Model_Idp extends Mage_Core_Model_Abstract
{
    /**
     * Constructor to init the idp resource
     */
    protected function _construct()
    {
        $this->_init('websso/idp');
    }
}