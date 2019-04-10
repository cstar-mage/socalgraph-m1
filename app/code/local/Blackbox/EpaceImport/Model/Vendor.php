<?php

class Blackbox_EpaceImport_Model_Vendor extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('epacei/vendor');
    }
}