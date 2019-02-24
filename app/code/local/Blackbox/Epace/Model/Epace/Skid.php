<?php

class Blackbox_Epace_Model_Epace_Skid extends Blackbox_Epace_Model_Epace_Shipment_ChildAbstract
{
    protected function _construct()
    {
        $this->_init('Skid', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'jobShipment' => 'int',
            'count' => 'int',
        ];
    }

    protected function getShipmentKey()
    {
        return 'jobShipment';
    }
}