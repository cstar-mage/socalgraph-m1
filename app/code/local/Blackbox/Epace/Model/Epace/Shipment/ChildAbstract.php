<?php

abstract class Blackbox_Epace_Model_Epace_Shipment_ChildAbstract extends Blackbox_Epace_Model_Epace_AbstractObject
{
    /**
     * @return int
     */
    public function getJobShipmentId()
    {
        return $this->getData($this->getShipmentKey());
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Shipment|bool
     */
    public function getJobShipment()
    {
        return $this->_getObject('jobShipment', $this->getShipmentKey(), 'efi/job_shipment');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Job_Shipment $shipment
     * @return $this
     */
    public function setJobShipment(Blackbox_Epace_Model_Epace_Job_Shipment $shipment)
    {
        return $this->_setObject('jobShipment', $shipment);
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Shipment|bool
     * @deprecated
     */
    public function getShipment()
    {
        return $this->getJobShipment();
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Job_Shipment $shipment
     * @return $this
     * @deprecated
     */
    public function setShipment(Blackbox_Epace_Model_Epace_Job_Shipment $shipment)
    {
        return $this->setJobShipment($shipment);
    }

    protected abstract function getShipmentKey();
}