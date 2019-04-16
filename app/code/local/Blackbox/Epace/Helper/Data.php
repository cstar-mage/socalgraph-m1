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

    public function getTypeName($objectType)
    {
        if ($objectType == 'EstimateQuoteLetterNote') {
            return 'estimate_quoteLetter_note';
        } else if ($objectType == 'FinishingOperationSpeed') {
            return 'finishingOperation_speed';
        }
        $re = '/(?(?<=[a-z])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z]))/x';
        $matches = preg_split($re, $objectType);
        $matches = array_map('lcfirst', $matches);
        $count = count($matches);
        for ($i = 0; $i < $count; $i++) {
            $type = implode('_', $matches);
            $object = Mage::getModel('efi/' . $type);
            if ($object) {
                return $type;
            }

            if ($count - $i > 1) {
                $matches[$count - $i - 2] = $matches[$count - $i - 2] . ucfirst($matches[$count - $i - 1]);
                unset($matches[$count - $i - 1]);
            }
        }

        return null;
    }
}