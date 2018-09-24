<?php

class Blackbox_CustomerStores_Model_Customer_Attribute_Source_Storelocator extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        $options = [];
        $stores = Mage::getResourceModel('storelocator/storelocator_collection');

        foreach ($stores as $item) {
            $options[] = [
                'value' => $item->getId(),
                'label' => $item->getName()
            ];
        }
        return $options;
    }
}