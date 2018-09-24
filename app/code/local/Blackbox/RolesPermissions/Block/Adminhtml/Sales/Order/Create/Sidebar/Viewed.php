<?php

class Blackbox_RolesPermissions_Block_Adminhtml_Sales_Order_Create_Sidebar_Viewed
    extends Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Viewed
{
    public function canDisplayPrice()
    {
        return Mage::helper('rolespermissions')->canViewPrices();
    }
}