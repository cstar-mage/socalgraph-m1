<?php

class Blackbox_Checkout_Block_Address_Modal extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        $this->setTemplate('blackbox/addressbook/address/modal.phtml');
    }

    protected function _beforeToHtml()
    {
        $this->getChild('grid')->setMultiple($this->getMultiple());
        return parent::_beforeToHtml();
    }
}