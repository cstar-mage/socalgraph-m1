<?php

class Blackbox_Epace_Model_Epace_Job_Component extends Blackbox_Epace_Model_Epace_Job_Part_AbstractChild
{
    protected function _construct()
    {
        $this->_init('JobComponent', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Ship_Via|bool
     */
    public function getShipVia()
    {
        return $this->_getObject('shipVia', 'shipVia', 'efi/ship_via', true);
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Ship_Via $shipVia
     * @return $this
     */
    public function setShipVia(Blackbox_Epace_Model_Epace_Ship_Via $shipVia)
    {
        return $this->_setObject('shipVia', $shipVia);
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Contact|bool
     */
    public function getShipToContact()
    {
        return $this->_getObject('contact', 'shipToContact', 'efi/contact');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Contact $contact
     * @return $this
     */
    public function setShipToContact(Blackbox_Epace_Model_Epace_Contact $contact)
    {
        return $this->_setObject('contact', $contact);
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

    public function getJobPartKeyField()
    {
        return 'jobPartKey';
    }
}