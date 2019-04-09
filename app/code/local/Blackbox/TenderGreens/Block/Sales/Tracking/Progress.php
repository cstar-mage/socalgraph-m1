<?php

class Blackbox_TenderGreens_Block_Sales_Tracking_Progress extends Mage_Core_Block_Template
{
    public function getProgressBarStep()
    {
        switch ($this->getCurrentProgressNumber()) {
            case 0:
                return 'zero';
            case 1:
                return 'first';
            case 2:
                return 'second';
            case 3:
                return 'third';
            case 4:
                return 'fourth';
            case 5:
                return 'fifth';

        }
    }

    protected function getCurrentProgressNumber()
    {
        $order = $this->getOrder();
        if ($order->getAccepted() || $order->getState() == 'closed') {
            return 4;
        }
        foreach ($order->getTrackingNumbers() as $trackingNumber) {
            return 3;
        }
        if ($order->hasShipments()) {
            return 2;
        }
        return 1;
    }

    protected function getTrackingInfo()
    {
        $tracks = $this->getOrder()->getTracksCollection();
        foreach ($tracks as $track){
            return $track->getNumberDetail();
        }
        return null;
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    public function getShippingDate()
    {
        $order = $this->getOrder();
        if (!$order->hasShipments()) {
            return '';
        }

        /** @var Mage_Sales_Model_Order_Shipment $shipment */
        $shipment = $order->getShipmentsCollection()->getFirstItem();

        return date('d F,Y', strtotime($shipment->getCreatedAt()));

    }
}