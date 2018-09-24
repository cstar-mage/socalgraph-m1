<?php

/**
 * Model for Adminhtml to render the session
 *
 * Class Wizkunde_WebSSO_Model_Session
 */
class Wizkunde_WebSSO_Model_Session extends Mage_Core_Model_Abstract
{
    /**
     * Constructor to init the session resource
     */
    protected function _construct()
    {
        $this->_init('websso/session');
    }
}