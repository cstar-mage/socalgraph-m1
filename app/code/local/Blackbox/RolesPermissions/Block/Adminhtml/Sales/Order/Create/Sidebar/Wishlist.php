<?php

class Blackbox_RolesPermissions_Block_Adminhtml_Sales_Order_Create_Sidebar_Wishlist
    extends Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Wishlist
{
    public function canDisplayPrice()
    {
        return Mage::helper('rolespermissions')->canViewPrices();
    }
}