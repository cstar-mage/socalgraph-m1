<?php
require_once Mage::getModuleDir('controllers', 'Mage_Downloadable').DS.'DownloadController.php';

class Blackbox_ProductFile_DownloadController extends Mage_Downloadable_DownloadController
{
    public function fileAction()
    {
        if (!$this->_getCustomerSession()->isLoggedIn()) {
            $this->_redirect('customer/account/login');
            return;
        }
        $productId = $this->getRequest()->getParam('product_id');
        if (!$productId) {
            $this->norouteAction();
            return;
        }
        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->getFile() || !file_exists(Mage::getBaseDir('media') . '/catalog/product' . $product->getFile())) {
            $this->norouteAction();
            return;
        }

        /** @var Mage_Downloadable_Model_Resource_Link_Purchased_Item_Collection $collection */
        $collection = Mage::getResourceModel('downloadable/link_purchased_item_collection');

        $collection->getSelect()->joinInner(array('p' => $collection->getTable('downloadable/link_purchased')), 'main_table.purchased_id = p.purchased_id', 'customer_id');

        $collection->addFieldToFilter('customer_id', $this->_getCustomerSession()->getCustomerId())
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('main_table.order_item_id', array('null' => true));

        /** @var Mage_Downloadable_Model_Link_Purchased_Item $linkPurchasedItem */
        $linkPurchasedItem = $collection->getFirstItem();
        if (!$linkPurchasedItem->getId()) {
            try {
                $linkPurchased = Mage::getModel('downloadable/link_purchased')->setData(array(
                    'order_id' => null,
                    'order_item_id' => null,
                    'created_at' => time(),
                    'updated_at' => time(),
                    'customer_id' => $this->_getCustomerSession()->getCustomerId(),
                    'product_name' => $product->getName(),
                    'product_sku' => $product->getSku(),
                    'link_section_title' => 'Links'
                ))->save();
                $linkPurchasedItem = Mage::getModel('downloadable/link_purchased_item')->setData(array(
                    'purchased_id' => $linkPurchased->getId(),
                    'order_item_id' => null,
                    'product_id' => $product->getId(),
                    'link_hash' => strtr(base64_encode(microtime() . $linkPurchased->getId() . $product->getId()), '+/=', '-_,'),
                    'number_of_downloads_bought' => 0,
                    'number_of_downloads_used' => 1,
                    'link_id' => null,
                    'link_title' => $product->getName(),
                    'is_shareable' => 1,
                    'link_url' => '',
                    'link_file' => '',
                    'link_type' => Mage_Downloadable_Helper_Download::LINK_TYPE_FILE,
                    'status' => 'available',
                    'created_at' => time(),
                    'updated_at' => time()
                ))->save();
            } catch (Exception $e) {
                $this->norouteAction();
                return;
            }
        } else {
            $linkPurchasedItem->setNumberOfDownloadsUsed($linkPurchasedItem->getNumberOfDownloadsUsed() + 1)->save();
        }

        return $this->_downloadFile($product->getFile());
    }

    public function linkAction()
    {
        $id = $this->getRequest()->getParam('id', 0);
        /** @var Mage_Downloadable_Model_Link_Purchased_Item $linkPurchasedItem */
        $linkPurchasedItem = Mage::getModel('downloadable/link_purchased_item')->load($id, 'link_hash');
        if (!$linkPurchasedItem->getId()) {
            $this->_getCustomerSession()->addNotice(Mage::helper('downloadable')->__("Requested link does not exist."));
            return $this->_redirect('*/customer/products');
        }
        if ($linkPurchasedItem->getLinkId()) {
            return parent::linkAction();
        }

        $product = Mage::getModel('catalog/product')->load($linkPurchasedItem->getProductId());
        if ($product->getFile()) {
            return $this->_downloadFile($product->getFile());
        }
        return $this->_redirectReferer();
    }

    protected function _downloadFile($file)
    {
        $resource = Mage::getBaseDir('media') . '/catalog/product' . $file;
        $resourceType = Mage_Downloadable_Helper_Download::LINK_TYPE_FILE;
        try {
            $this->_processDownload($resource, $resourceType);
            exit(0);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError(Mage::helper('downloadable')->__('Sorry, there was an error getting requested content. Please contact the store owner.'));
        }
        return $this->_redirectReferer();
    }
}