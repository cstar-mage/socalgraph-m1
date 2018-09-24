<?php

class Blackbox_LoginHistory_Model_Resource_Login extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('login_history/login', 'id');
    }

    public function getCustomerCountries($customerId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'country_id')
            ->where('customer_id = ?', $customerId)
            ->group('country_id');
        return $this->_getReadAdapter()->fetchCol($select);
    }

    public function getCustomerCities($customerId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'city')
            ->where('customer_id = ?', $customerId)
            ->group('country_id');
        return $this->_getReadAdapter()->fetchCol($select);
    }
}