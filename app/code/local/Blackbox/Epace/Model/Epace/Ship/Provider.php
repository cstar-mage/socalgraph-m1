<?php

/**
 * @method string getName()
 * @method bool getActive()
 *
 * Class Blackbox_Epace_Model_Epace_Ship_Provider
 */
class Blackbox_Epace_Model_Epace_Ship_Provider extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('ShipProvider', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Ship_Via[]
     */
    public function getShipVias()
    {
        return $this->_getChildItems('efi/ship_via_collection', [
            'provider' => $this->getId()
        ], function ($item) {
            $item->setShipProvider($this);
        });
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'name' => 'string',
            'trackingUrl' => 'string',
            'active' => 'bool',
        ];
    }
}