<?php

class Blackbox_Barcode_Model_Form_Element_Renderer_Barcode extends Varien_Data_Form_Element_Abstract
{
    public function getElementHtml()
    {
        $block = Mage::app()->getLayout()->getBlock('barcode_renderer');
        if (!$block) {
            $block = Mage::app()->getLayout()->createBlock('barcode/adminhtml_catalog_product_edit_renderer_barcode');
        }
        return $block->toHtml();
    }
}