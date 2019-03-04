<?php

abstract class Blackbox_Epace_Model_Epace_Shipment_ChildAbstract extends Blackbox_Epace_Model_Epace_AbstractObject
{
    /**
     * @return Blackbox_Epace_Model_Epace_Job_Shipment|bool
     */
    public function getShipment()
    {
        return $this->_getObject('shipment', $this->getShipmentKey(), 'efi/job_shipment');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Job_Shipment $shipment
     * @return $this
     */
    public function setShipment(Blackbox_Epace_Model_Epace_Job_Shipment $shipment)
    {
        return $this->_setObject('shipment', $shipment);
    }

    protected abstract function getShipmentKey();
}