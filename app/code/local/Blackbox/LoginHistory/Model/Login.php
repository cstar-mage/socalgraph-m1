<?php

/**
 * @method int getCustomerId()
 * @method $this setCustomerId(int $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method int getRemoteAddr()
 * @method $this setRemoteAddr(int $value)
 * @method string getDate()
 * @method $this setDate(string $value)
 * @method string getCountryId()
 * @method $this setCountryId(string $value)
 * @method string getCity()
 * @method $this setCity(string $value)
 *
 * Class Blackbox_LoginHistory_Model_Login
 */
class Blackbox_LoginHistory_Model_Login extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('login_history/login');
    }
}