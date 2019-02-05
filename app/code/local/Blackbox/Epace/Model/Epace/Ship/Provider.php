<?php

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
        /** @var Blackbox_Epace_Model_Resource_Epace_Ship_Via_Collection $collection */
        $collection = Mage::getResourceModel('efi/ship_via_collection');
        $items = $collection->addFilter('provider', $this->getId())->getItems();
        foreach ($items as $item) {
            $item->setShipProvider($this);
        }
        return $items;
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