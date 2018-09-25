<?php

class Blackbox_Barcode_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getBarcodeFiletype()
    {
        return Mage::getStoreConfig('barcode/settings/filetype');
    }

    public function getBarcodeType()
    {
        return Mage::getStoreConfig('barcode/settings/type');
    }

    public function getBarcodeDataSource()
    {
        return Mage::getStoreConfig('barcode/settings/data_source');
    }

    public function getBarcodeWidthFactor()
    {
        return Mage::getStoreConfig('barcode/settings/width_factor');
    }

    public function getBarcodeTotalHeight()
    {
        return Mage::getStoreConfig('barcode/settings/height');
    }

    public function getBarcodeColor()
    {
        return Mage::getStoreConfig('barcode/settings/color');
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return Blackbox_Barcode_Model_Barcode
     */
    public function getBarcode(Mage_Catalog_Model_Product $product)
    {
        $class = Mage::getConfig()->getModelClassName('barcode/barcode');
        /** @var Blackbox_Barcode_Model_Barcode $barcode */
        $barcode = new $class($product, $this->getBarcodeFiletype(), $this->getBarcodeType());
        $barcode->setWidthFactor($this->getBarcodeWidthFactor())
            ->setTotalHeight($this->getBarcodeTotalHeight())
            ->setColor($this->getBarcodeColor());
        return $barcode;
    }
}