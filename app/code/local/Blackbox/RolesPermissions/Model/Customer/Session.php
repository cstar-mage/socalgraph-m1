<?php

class Blackbox_RolesPermissions_Model_Customer_Session extends Mage_Customer_Model_Session
{
    public function __construct()
    {
        if (!Mage::helper('rolespermissions')->isEnabled()) {
            parent::__construct();
            return;
        }

        $namespace = 'customer';

        $this->init($namespace, 'frontend');
        Mage::dispatchEvent('customer_session_init', array('customer_session'=>$this));

        $this->getCustomer();
    }
}