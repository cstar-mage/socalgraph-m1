<?php

class Blackbox_Barcode_Model_System_Config_Source_Filetype
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'jpg', 'label'=>Mage::helper('adminhtml')->__('JPG')),
            array('value' => 'png', 'label'=>Mage::helper('adminhtml')->__('PNG')),
            array('value' => 'svg', 'label'=>Mage::helper('adminhtml')->__('SVG')),
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
            'jpg' => Mage::helper('adminhtml')->__('JPG'),
            'png' => Mage::helper('adminhtml')->__('PNG'),
            'svg' => Mage::helper('adminhtml')->__('SVG'),
        );
    }
}