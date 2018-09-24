<?php

class Cmsmart_Onlinedesign_Model_Mysql4_Color extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the onlinedesign_id refers to the key field in your database table.
        $this->_init('onlinedesign/color', 'color_id');
    }
}