<?php

class Blackbox_ProductFile_Model_Link_Purchased extends Mage_Downloadable_Model_Link_Purchased
{
    public function _beforeSave()
    {
        return Mage_Core_Model_Abstract::_beforeSave();
    }
}