<?php

class Blackbox_EpaceImport_Model_Resource_Receivable extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('epacei/receivable', 'entity_id');
    }
}