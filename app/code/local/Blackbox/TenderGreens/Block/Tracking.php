<?php

class Blackbox_TenderGreens_Block_Tracking extends Mage_Core_Block_Template
{
    /**
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    protected function _construct()
    {
        $this->setTemplate('blackbox/tendergreens/tracking.phtml');
        parent::_construct();
    }
}