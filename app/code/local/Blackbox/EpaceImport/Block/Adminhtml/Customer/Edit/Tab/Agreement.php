<?php

class Blackbox_EpaceImport_Block_Adminhtml_Customer_Edit_Tab_Agreement extends Mage_Sales_Block_Adminhtml_Customer_Edit_Tab_Agreement
{
    public function getTabLabel()
    {
        return $this->__('Billing');
    }

    public function getTabTitle()
    {
        return $this->__('Billing');
    }
}