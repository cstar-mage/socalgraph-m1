<?php

class Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_Renderer_Company extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{	

    public function render(Varien_Object $row)
    {
        $entityId =  $row->getData('entity_id');
        $po = Mage::getModel('epacei/purchaseOrder')->load($entityId);
        if($po->getVendorAddress()){
            return $po->getVendorAddress()->getCompany();
        }

        return $po->getCompany();
    }
}