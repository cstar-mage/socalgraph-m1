<?php

class Blackbox_RolesPermissions_Block_Adminhtml_Dashboard_Tab_Products_Ordered
    extends Mage_Adminhtml_Block_Dashboard_Tab_Products_Ordered
{
    public function _prepareColumns()
    {
        parent::_prepareColumns();

        if (!Mage::helper('rolespermissions')->canViewPrices()) {
            $this->removeColumn('price');
        }

        return $this;
    }
}