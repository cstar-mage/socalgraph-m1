<?php

class Blackbox_Epace_Model_Mysql4_Event_File extends Mage_Core_Model_Mysql4_Abstract {
    protected function _construct() {
        $this->_init('epace/event_file', 'id');
    }
}
