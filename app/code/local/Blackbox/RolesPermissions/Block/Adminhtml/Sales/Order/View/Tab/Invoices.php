<?php

class Blackbox_RolesPermissions_Block_Adminhtml_Sales_Order_View_Tab_Invoices
    extends Mage_Adminhtml_Block_Sales_Order_View_Tab_Invoices
{
    public function _prepareColumns()
    {
        parent::_prepareColumns();

        if (!Mage::helper('rolespermissions')->canViewPrices()) {
            $this->removeColumn('base_grand_total');
        }

        return $this;
    }
}