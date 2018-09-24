<?php

class Blackbox_RolesPermissions_Block_Wishlist_Customer_Wishlist_Item_Column_Cart
    extends Mage_Wishlist_Block_Customer_Wishlist_Item_Column_Cart
{
    /**
     * Checks whether column should be shown in table
     *
     * @return bool
     */
    public function isEnabled()
    {
        if ($this->getCheckPrice()) {
            return Mage::helper('rolespermissions')->canViewPrices();
        }
        return true;
    }
}