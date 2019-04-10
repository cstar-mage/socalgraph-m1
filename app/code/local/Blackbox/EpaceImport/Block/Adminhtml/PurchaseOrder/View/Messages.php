<?php
/**
 * PurchaseOrder view messages
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_View_Messages extends Mage_Adminhtml_Block_Messages
{

    protected function _getPurchaseOrder()
    {
        return Mage::registry('purchase_order');
    }

    public function _prepareLayout()
    {
        /**
         * Check customer existing
         */
        $customer = Mage::getModel('customer/customer')->load($this->_getPurchaseOrder()->getCustomerId());

        /**
         * Check Item products existing
         */
        $productIds = array();
        foreach ($this->_getPurchaseOrder()->getAllItems() as $item) {
            $productIds[] = $item->getProductId();
        }

        return parent::_prepareLayout();
    }

}
