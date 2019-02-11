<?php

class Blackbox_Epace_Model_Epace_Job_Component extends Blackbox_Epace_Model_Epace_Job_Part_AbstractChild
{
    /**
     * @var Blackbox_Epace_Model_Epace_Ship_Via
     */
    protected $shipVia;

    /**
     * @var Blackbox_Epace_Model_Epace_Contact
     */
    protected $contact;

    /**
     * @var Blackbox_Epace_Model_Epace_Contact
     */
    protected $shipToContact;

    protected function _construct()
    {
        $this->_init('JobComponent', 'id');
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
     * @return Blackbox_Epace_Model_Epace_Contact|bool
     */
    public function getShipToContact()
    {
        if (is_null($this->contact)) {
            $this->contact = false;
            if ($this->getData('shipToContact')) {
                $contact = Mage::helper('epace/object')->load('efi/contact', $this->getData('shipToContact'));
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
    public function setShipToContact(Blackbox_Epace_Model_Epace_Contact $contact)
    {
        $this->contact = $contact;

        return $this;
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'job' => 'string',
            'jobPart' => 'string',
            'description' => 'string',
            'finalSizeH' => 'float',
            'finalSizeW' => 'float',
            'shipVia' => 'int',
            'terms' => 'int',
            'productionStatus' => 'string',
            'qtyOrdered' => 'int',
            'qtyToMfg' => 'int',
            'shipToContact' => 'int',
            'colorsS1' => 'int',
            'colorsS2' => 'int',
            'colorsTotal' => 'int',
            'numSigs' => 'int',
            'active' => 'bool',
            'quantityRemaining' => 'float',
            'jobPartKey' => 'string',
        ];
    }

    public function getJobPartKey()
    {
        return $this->getData('jobPartKey');
    }
}