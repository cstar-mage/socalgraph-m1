<?php

class Blackbox_EpaceImport_Block_Adminhtml_Sales_Order_View extends Blackbox_OrderApproval_Block_Adminhtml_Sales_Order_View//Mage_Adminhtml_Block_Sales_Order_View
{
    public function __construct()
    {
        parent::__construct();

        if ($jobId = $this->getOrder()->getEpaceJobId()) {
            $this->addButton('go_to_epace', [
                'label' => Mage::helper('sales')->__('Go to Epace'),
                'onclick' => 'popWin(\'' . $this->getEpaceUrl($jobId) . '\', \'_blank\', null)'
            ]);
        }
    }

    public function getEpaceUrl($jobId)
    {
        /** @var Blackbox_Epace_Helper_Api $api */
        $api = Mage::helper('epace/api');
        return $api->getHost() . '/epace/company:public/object/Job/detail/' . $jobId;
    }
}