<?php

class Blackbox_RolesPermissions_Model_Admin_User extends Mage_Admin_Model_User
{
    public function getCustomer()
    {
        if (!($customer = parent::getCustomer())) {
            $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
            parent::setCustomer($customer);
        }
        return $customer;
    }

    public function getDisplayUsername()
    {
        if ($this->getCustomerId()) {
            return $this->getCustomer()->getLogin();
        }
        parent::getUsername();
    }

    public function getFirstname()
    {
        if ($this->getCustomerId()) {
            return $this->getCustomer()->getFirstname();
        }
        parent::getFirstname();
    }

    public function getLastname()
    {
        if ($this->getCustomerId()) {
            return $this->getCustomer()->getLastname();
        }
        return parent::getLastname();
    }

    public function getEmail()
    {
        if ($this->getCustomerId()) {
            return $this->getCustomer()->getEmail();
        }
        return parent::getEmail();
    }
}