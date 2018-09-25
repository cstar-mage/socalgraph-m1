<?php

class Blackbox_Barcode_Model_Eav_Entity_Attribute_Source_Data extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    const VALUE_DEFAULT = '';
    const VALUE_SKU = 'sku';
    const VALUE_INVENTORY_NUMBER = 'inventory_number';

    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'label' => Mage::helper('eav')->__('Default'),
                    'value' => self::VALUE_DEFAULT
                ),
                array(
                    'label' => Mage::helper('eav')->__('Sku'),
                    'value' => self::VALUE_SKU
                ),
                array(
                    'label' => Mage::helper('eav')->__('Inventory Number'),
                    'value' => self::VALUE_INVENTORY_NUMBER
                ),
            );
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = array();
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }
}