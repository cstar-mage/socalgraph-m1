<?php

abstract class Blackbox_Epace_Model_Epace_Shipment_ChildAbstract extends Blackbox_Epace_Model_Epace_AbstractObject
{
    /**
     * @var Blackbox_Epace_Model_Epace_Job_Shipment
     */
    protected $shipment;

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Shipment|bool
     */
    public function getShipment()
    {
        if (is_null($this->shipment)) {
            $this->shipment = false;
            if ($this->getShipmentKey()) {
                $shipment = Mage::getModel('efi/job_shipment')->load($this->getShipmentKey());
                if ($shipment->getId()) {
                    $this->shipment = $shipment;
                }
            }
        }

        return $this->shipment;
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Job_Shipment $shipment
     * @return $this
     */
    public function setShipment(Blackbox_Epace_Model_Epace_Job_Shipment $shipment)
    {
        $this->shipment = $shipment;

        return $this;
    }

    protected abstract function getShipmentKey();
}