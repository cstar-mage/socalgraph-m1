<?php

/**
 * @method string getDate()
 * @method bool getDateForced()
 * @method string getTime()
 * @method bool getTimeForced()
 * @method string getPromiseDate()
 * @method int getShipmentType()
 * @method float getCost()
 * @method float getWeight()
 * @method string getShipperName()
 * @method string getTrackingNumber()
 * @method string getNotes()
 * @method bool getPackageDrop()
 * @method string getName()
 * @method string getAddress1()
 * @method string getCity()
 * @method string getState()
 * @method int getZip()
 * @method int getCountry()
 * @method string getEmail()
 * @method string getContactFirstName()
 * @method string getContactLastName()
 * @method int getShipInNameOf()
 * @method int getContactNumber()
 * @method bool getSaturday()
 * @method bool getShipToInventory()
 * @method bool getPlanned()
 * @method bool getUseLegacyPrintFlowFormat()
 * @method bool getFromEservice()
 * @method string getCharges()
 * @method int getManufacturingLocation()
 * @method bool getCodCompanyCheckAcceptable()
 * @method bool getFreightLinkIntegrated()
 * @method bool getShipped()
 * @method bool getProof()
 * @method bool getAutoCreated()
 * @method bool getCollapsedInUi()
 * @method bool getSentToDSF()
 * @method string getPackageDropType()
 * @method string getQuantityRemaining()
 * @method float getQuantityStillToReceive()
 * @method int getQuantity()
 * @method string getTrackingLink()
 * @method bool getNewItem()
 * @method bool getBillOfLadingAdd()
 * @method bool getScheduled()
 * @method string getStateKey()
 *
 * Class Blackbox_Epace_Model_Epace_Job_Shipment
 */
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
     * @return string
     */
    public function getU_processShipperKeySav()
    {
        return $this->getData('U_processShipperKeySav');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setU_processShipperKeySav($value)
    {
        return $this->setData('U_processShipperKeySav', $value);
    }

    /**
     * @return string
     */
    public function getU_processShipperID()
    {
        return $this->getData('U_processShipperID');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setU_processShipperID($value)
    {
        return $this->setData('U_processShipperID', (string)$value);
    }

    /**
     * @return Blackbox_Epace_Model_Epace_ShipmentType|bool
     */
    public function getType()
    {
        return $this->_getObject('type', 'shipmentType', 'efi/shipmentType', true);
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
     * @return int
     */
    public function getContactId()
    {
        return $this->getData('contactNumber');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Contact|bool
     */
    public function getContact()
    {
        if (is_null($this->contact)) {
            $this->contact = false;
            if ($this->getData('contactNumber')) {
                $contact = Mage::helper('epace/object')->load('efi/contact', $this->getData('contactNumber'));
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
     * @return int
     */
    public function getJobContactId()
    {
        return $this->getData('jobContact');
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
        return $this->_getChildItems('efi/carton_collection', [
            'shipment' => $this->getId()
        ], function ($item) {
            $item->setShipment($this);
        });
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Skid[]
     */
    public function getSkids()
    {
        return $this->_getChildItems('efi/skid_collection', [
            'jobShipment' => $this->getId()
        ], function ($item) {
            $item->setShipment($this);
        });
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'job' => 'string',
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