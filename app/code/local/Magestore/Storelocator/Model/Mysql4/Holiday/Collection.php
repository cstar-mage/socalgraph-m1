<?php

class Magestore_Storelocator_Model_Mysql4_Holiday_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('storelocator/holiday');
    }
}