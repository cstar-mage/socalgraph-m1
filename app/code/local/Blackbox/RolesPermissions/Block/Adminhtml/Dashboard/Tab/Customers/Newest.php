<?php

class Blackbox_RolesPermissions_Block_Adminhtml_Dashboard_Tab_Customers_Newest
    extends Mage_Adminhtml_Block_Dashboard_Tab_Customers_Newest
{
    public function _prepareColumns()
    {
        parent::_prepareColumns();

        if (!Mage::helper('rolespermissions')->canViewPrices()) {
            $this->removeColumn('orders_avg_amount');
            $this->removeColumn('orders_sum_amount');
        }

        return $this;
    }
}