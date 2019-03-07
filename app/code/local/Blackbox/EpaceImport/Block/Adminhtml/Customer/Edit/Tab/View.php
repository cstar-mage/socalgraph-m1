<?php

class Blackbox_EpaceImport_Block_Adminhtml_Customer_Edit_Tab_View extends Mage_Adminhtml_Block_Customer_Edit_Tab_View
{
    public function getTabLabel()
    {
        return Mage::helper('customer')->__('Customers');
    }

    public function getTabTitle()
    {
        return Mage::helper('customer')->__('Customers');
    }
}