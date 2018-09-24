<?php

class Blackbox_ProductFile_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $downloadsUsed = [];

    /**
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @return int
     */
    public function getDownloadsUsed($orderItem)
    {
        if (!array_key_exists($orderItem->getId(), $this->downloadsUsed)) {
            $this->downloadsUsed[$orderItem->getId()] = $this->_getDownloadsUsed($orderItem);
        }
        return $this->downloadsUsed[$orderItem->getId()];
    }

    /**
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @return int
     */
    protected function _getDownloadsUsed($orderItem)
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();

        /** @var Mage_Downloadable_Model_Resource_Link_Purchased_Item_Collection $purchasedItemCollection */
        $purchasedItemCollection = Mage::getResourceModel('downloadable/link_purchased_item_collection');

        $select = $purchasedItemCollection->getSelect()
            ->joinInner(['link_purchased' => $purchasedItemCollection->getResource()->getTable('downloadable/link_purchased')], 'main_table.purchased_id = link_purchased.purchased_id', 'customer_id')
            ->where('link_purchased.customer_id = ' . $customer->getId() . ' AND main_table.product_id = ' . $orderItem->getProductId())
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(['downloads' => new Zend_Db_Expr('SUM(number_of_downloads_used)')]);

        $data = $purchasedItemCollection->getResource()->getReadConnection()->fetchAll($select);

        return (int)$data[0]['downloads'];
    }

    /**
     * @param Mage_Catalog_model_Product|int $product
     */
    public function isFileExists($product)
    {
        if (!is_object($product)) {
            $product = Mage::getModel('catalog/product')->load($product);
        }

        return $product->getFile() && file_exists(Mage::getBaseDir('media') . '/catalog/product' . $product->getFile());
    }
}