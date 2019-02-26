<?php

/**
 * @method string getDescription()
 *
 * Class Blackbox_Epace_Model_Epace_Ship_Via
 */
class Blackbox_Epace_Model_Epace_Ship_Via extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('ShipVia', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Ship_Provider|bool
     */
    public function getShipProvider()
    {
        return $this->_getObject('provider', 'provider', 'efi/ship_provider', true);
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Ship_Provider $provider
     * @return $this
     */
    public function setShipProvider(Blackbox_Epace_Model_Epace_Ship_Provider $provider)
    {
        return $this->_setObject('provider', $provider);
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'provider' => 'int',
            'description' => 'string',
            'minimumWeight' => '',
            'maximumWeight' => '',
            'multiBoxShipping' => 'bool',
            'maxWeightPerBox' => '',
            'active' => 'bool',
            'daysintransit' => '',
            'cutOffTime' => '',
            'earliestDeliveryTime' => '',
            'dateCalcType' => '',
            'dsfDeliveryMethod' => 'bool',
            'availForRelay' => 'bool',
            'dsfShared' => 'bool',
            'billOfLading' => 'bool',
            'activityCode' => '',
            'availableInEcommerce' => 'bool',
        ];
    }
}