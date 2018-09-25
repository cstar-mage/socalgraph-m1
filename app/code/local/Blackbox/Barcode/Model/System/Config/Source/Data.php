<?php

class Blackbox_Barcode_Model_System_Config_Source_Data
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'inventory_number', 'label'=>Mage::helper('adminhtml')->__('Inventory Number')),
            array('value' => 'sku', 'label'=>Mage::helper('adminhtml')->__('Sku')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'inventory_number' => Mage::helper('adminhtml')->__('Inventory Number'),
            'sku' => Mage::helper('adminhtml')->__('Sku'),
        );
    }
}