<?php

class Blackbox_Barcode_Model_Observer
{
    protected $products = [];

    public function productSaveBefore($observer)
    {
        $product = $observer->getProduct();
        if ($product->getOrigData($this->getOrigDataSource($product)) != $product->getData($this->getDataSource($product))) {
            $this->products[] = $product;
        }

    }

    public function productSaveCommitAfter($observer)
    {
        $product = $observer->getProduct();
        if (($key = array_search($product, $this->products)) !== false) {
            unset($this->products[$key]);
            $barcode = $this->getHelper()->getBarcode($product);
            $barcode->delete();
        }
    }

    public function productDeleteAfter($observer)
    {
        $product = $observer->getProduct();
        $barcode = $this->getHelper()->getBarcode($product);
        $barcode->delete();
    }

    protected function getDataSource(Mage_Catalog_Model_Product $product)
    {
        return $product->getBarcodeDataSource() ?: $this->getHelper()->getBarcodeDataSource();
    }

    protected function getOrigDataSource(Mage_Catalog_Model_Product $product)
    {
        return $product->getOrigData('barcode_data_source') ?: $this->getHelper()->getBarcodeDataSource();
    }

    /**
     * @return Blackbox_Barcode_Helper_Data
     */
    protected function getHelper()
    {
        return Mage::helper('barcode');
    }
}