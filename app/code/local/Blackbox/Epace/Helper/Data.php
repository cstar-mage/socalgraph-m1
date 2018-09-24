<?php

class Blackbox_Epace_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_settingsPath = 'epace/main_settings/';

    public function isEnabled()
    {
        return Mage::getStoreConfigFlag($this->_settingsPath . 'enable');
    }

    public function isLiveMode()
    {
        return Mage::getStoreConfigFlag($this->_settingsPath . 'mode');
    }
}