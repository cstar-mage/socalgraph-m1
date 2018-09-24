<?php
 
class Blackbox_Epace_Model_Mysql4_Event_File_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    protected function _construct() {
        $this->_init('epace/event_file');
    }
}
