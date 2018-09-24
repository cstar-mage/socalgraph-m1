<?php

require_once 'abstract.php';

class Mage_Shell_MaxApproval extends Mage_Shell_Abstract
{
    public function run()
    {
        $collection = Mage::getResourceModel('catalog/product_collection');
        /** @var Mage_Catalog_Model_Product $product */
        foreach ($collection as $product) {
            /** @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
            $stockItem = Mage::getModel('cataloginventory/stock_item');
            $stockItem->loadByProduct($product->getId());

            if ($qty = $stockItem->getData('max_sale_qty')) {
                $product->setData('max_approval', (int)$qty);
                $product->getResource()->saveAttribute($product, 'max_approval');
            }
        }
    }
}

$shell = new Mage_Shell_MaxApproval();
$shell->run();