<?php

class Blackbox_Epace_Model_Epace_Carton extends Blackbox_Epace_Model_Epace_AbstractObject
{
    /** @var  Blackbox_Epace_Model_Epace_Job_Shipment */
    protected $shipment;

    protected function _construct()
    {
        $this->_init('Carton', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Shipment|bool
     */
    public function getShipment()
    {
        if (is_null($this->shipment)) {
            $this->shipment = false;
            if ($this->getData('shipment')) {
                $shipment = Mage::getModel('efi/job_shipment')->load($this->getData('shipment'));
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

    /**
     * @return Blackbox_Epace_Model_Epace_Carton_Content[]
     */
    public function getContents()
    {
        /** @var Blackbox_Epace_Model_Resource_Epace_Carton_Content_Collection $collection */
        $collection = Mage::getResourceModel('efi/carton_content_collection');
        $items = $collection->addFilter('carton', $this->getId())->getItems();
        foreach ($items as $item) {
            $item->setCarton($this);
        }
        return $items;
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'shipment' => '',
            'count' => '',
            'quantity' => '',
            'addDefaultContent' => 'bool',
            'note' => '',
            'trackingNumber' => '',
            'actualDate' => '',
            'actualTime' => '',
            'skidCount' => '',
            'weight' => '',
            'cost' => '',
            'trackingLink' => '',
            'totalQuantity' => '',
            'totalSkidQuantity' => '',
        ];
    }
}