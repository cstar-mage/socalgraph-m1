<?php

class Blackbox_TenderGreens_Block_Popup_Confirm extends Mage_Core_Block_Template
{
    public function _construct()
    {
        $this->setTemplate('blackbox/popup/confirm.phtml');
        parent::_construct();
    }
}