<?php

/**
 * Class Blackbox_Barcode_Block_Adminhtml_Catalog_Product_Edit_Renderer_Barcode
 */
class Blackbox_Barcode_Block_Adminhtml_Catalog_Product_Edit_Renderer_Barcode extends Mage_Core_Block_Template
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('blackbox/barcode/renderer/barcode.phtml');
    }

    public function getImageUrl()
    {
        return $this->_getHelper()->getBarcode($this->getProduct())->getUrl(true);
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * @return Blackbox_Barcode_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('barcode');
    }
}