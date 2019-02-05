<?php

class Blackbox_Epace_Model_Epace_Job_Shipment extends Blackbox_Epace_Model_Epace_Job_AbstractChild
{
    /**
     * @var  Blackbox_Epace_Model_Epace_ShipmentType
     */
    protected $type;

    /**
     * @var Blackbox_Epace_Model_Epace_Contact
     */
    protected $contact = null;

    /**
     * @var Blackbox_Epace_Model_Epace_Job_Contact
     */
    protected $jobContact = null;

    /**
     * @var Blackbox_Epace_Model_Epace_Ship_Via
     */
    protected $shipVia = null;

    protected function _construct()
    {
        $this->_init('JobShipment', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_ShipmentType|bool
     */
    public function getType()
    {
        if (is_null($this->type)) {
            $this->type = false;
            if ($this->getData('shipmentType')) {
                $shipmentType = Mage::helper('epace/object')->load('efi/shipmentType', $this->getData('shipmentType'));
                if ($shipmentType->getId()) {
                    $this->type = $shipmentType;
                }
            }
        }

        return $this->type;
    }

    /**
     * @param Blackbox_Epace_Model_Epace_ShipmentType $contact
     * @return $this
     */
    public function setType(Blackbox_Epace_Model_Epace_ShipmentType $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Contact|bool
     */
    public function getContact()
    {
        if (is_null($this->contact)) {
            $this->contact = false;
            if ($this->getData('contactNumber')) {
                $contact = Mage::getModel('efi/contact')->load($this->getData('contactNumber'));
                if ($contact->getId()) {
                    $this->contact = $contact;
                }
            }
        }

        return $this->contact;
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Contact $contact
     * @return $this
     */
    public function setContact(Blackbox_Epace_Model_Epace_Contact $contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Contact|bool
     */
    public function getJobContact()
    {
        if (is_null($this->jobContact)) {
            $this->jobContact = false;
            if ($this->getData('jobContact')) {
                $jobContact = Mage::getModel('efi/job_contact')->load($this->getData('jobContact'));
                if ($jobContact->getId()) {
                    $this->jobContact = $jobContact;
                }
            }
        }

        return $this->jobContact;
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Job_Contact $contact
     * @return $this
     */
    public function setJobContact(Blackbox_Epace_Model_Epace_Contact $jobContact)
    {
        $this->jobContact = $jobContact;

        return $this;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Ship_Via|bool
     */
    public function getShipVia()
    {
        if (is_null($this->shipVia)) {
            $this->shipVia = false;
            if ($this->getData('shipVia')) {
                $shipVia = Mage::getModel('efi/ship_via')->load($this->getData('shipVia'));
                if ($shipVia->getId()) {
                    $this->shipVia = $shipVia;
                }
            }
        }

        return $this->shipVia;
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Ship_Via $shipVia
     * @return $this
     */
    public function setShipVia(Blackbox_Epace_Model_Epace_Ship_Via $shipVia)
    {
        $this->shipVia = $shipVia;

        return $this;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Carton[]
     */
    public function getCartons()
    {
        /** @var Blackbox_Epace_Model_Resource_Epace_Carton_Collection $collection */
        $collection = Mage::getResourceModel('efi/carton_collection');
        $items = $collection->addFilter('shipment', $this->getId())->getItems();
        foreach ($items as $item) {
            $item->setShipment($this);
        }
        return $items;
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'job' => '',
            'date' => '',
            'dateForced' => 'bool',
            'time' => '',
            'timeForced' => 'bool',
            'promiseDate' => '',
            'promiseTime' => '',
            'shipmentType' => '',
            'quotedPrice' => '',
            'shipperName' => '',
            'packageDrop' => 'bool',
            'name' => '',
            'address1' => '',
            'city' => '',
            'state' => '',
            'zip' => '',
            'country' => '',
            'email' => '',
            'contactFirstName' => '',
            'contactLastName' => '',
            'shipInNameOf' => '',
            'contactNumber' => '',
            'shipVia' => 'int',
            'saturday' => 'bool',
            'shipToInventory' => 'bool',
            'planned' => 'bool',
            'useLegacyPrintFlowFormat' => 'bool',
            'fromEservice' => 'bool',
            'charges' => '',
            'plannedQuantity' => '',
            'jobContact' => '',
            'dsfShippingDetailID' => '',
            'manufacturingLocation' => '',
            'codCompanyCheckAcceptable' => 'bool',
            'freightLinkIntegrated' => 'bool',
            'dsfProductID' => '',
            'shipped' => 'bool',
            'proof' => 'bool',
            'autoCreated' => 'bool',
            'collapsedInUi' => 'bool',
            'sentToDSF' => 'bool',
            'costDistribution' => '',
            'packageDropType' => '',
            'quantityRemaining' => '',
            'quantityStillToReceive' => '',
            'quantity' => '',
            'newItem' => 'bool',
            'billOfLadingAdd' => 'bool',
            'scheduled' => 'bool',
            'stateKey' => '',
        ];
    }
}