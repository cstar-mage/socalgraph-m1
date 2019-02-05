<?php

class Blackbox_Epace_Model_Epace_Ship_Via extends Blackbox_Epace_Model_Epace_AbstractObject
{
    /**
     * @var Blackbox_Epace_Model_Epace_Ship_Provider
     */
    protected $provider = null;

    protected function _construct()
    {
        $this->_init('ShipVia', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Ship_Provider|bool
     */
    public function getShipProvider()
    {
        if (is_null($this->provider)) {
            $this->provider = false;
            if ($this->getData('provider')) {
                $provider = Mage::getModel('efi/ship_provider')->load($this->getData('provider'));
                if ($provider->getId()) {
                    $this->provider = $provider;
                }
            }
        }

        return $this->provider;
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Ship_Provider $provider
     * @return $this
     */
    public function setShipProvider(Blackbox_Epace_Model_Epace_Ship_Provider $provider)
    {
        $this->provider = $provider;

        return $this;
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