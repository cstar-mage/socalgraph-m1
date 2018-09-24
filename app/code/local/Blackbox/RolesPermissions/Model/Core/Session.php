<?php

class Blackbox_RolesPermissions_Model_Core_Session extends Mage_Core_Model_Session
{
    /**
     * Init session with namespace
     *
     * @param string $namespace
     * @param string $sessionName
     * @return Mage_Core_Model_Session_Abstract_Varien
     */
    public function init($namespace, $sessionName=null)
    {
        if (!Mage::helper('rolespermissions')->isEnabled()) {
            return parent::init($namespace, $sessionName);
        }
        return parent::init($namespace, 'general');
    }
}