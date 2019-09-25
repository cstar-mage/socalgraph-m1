<?php

class Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_Renderer_JobId extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{	

    public function render(Varien_Object $row)
    {
        $entityId =  $row->getData('entity_id');
        $po = Mage::getModel('epacei/purchaseOrder')->load($entityId);
        $lines = $po->getAllItems();
        $jobid = '';
        if(count($lines)){
            $orderId = $lines[0]->getOrderId();
            if($orderId) {
                $job = Mage::getModel('sales/order')->load($orderId);
                if($job) {
                    $jobid = $job->getEpaceJobId();
                }
            }
        }
        
        return $jobid;
    }
}