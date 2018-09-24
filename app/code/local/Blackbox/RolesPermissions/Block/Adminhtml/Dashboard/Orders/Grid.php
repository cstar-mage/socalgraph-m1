<?php

class Blackbox_RolesPermissions_Block_Adminhtml_Dashboard_Orders_Grid
    extends Mage_Adminhtml_Block_Dashboard_Orders_Grid
{
    public function _prepareColumns()
    {
        parent::_prepareColumns();

        if (!Mage::helper('rolespermissions')->canViewPrices()) {
            $this->removeColumn('total');
        }

        return $this;
    }
}