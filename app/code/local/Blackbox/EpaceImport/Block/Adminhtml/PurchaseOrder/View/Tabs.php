<?php
/**
 * PurchaseOrder view tabs
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_View_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Retrieve available purchase order
     *
     * @return Blackbox_EpaceImport_Model_PurchaseOrder
     */
    public function getPurchaseOrder()
    {
        if ($this->hasPurchaseOrder()) {
            return $this->getData('purchase_order');
        }
        if (Mage::registry('current_purchase_order')) {
            return Mage::registry('current_purchase_order');
        }
        if (Mage::registry('purchase_order')) {
            return Mage::registry('purchase_order');
        }
        Mage::throwException(Mage::helper('epacei')->__('Cannot get the purchase order instance.'));
    }

    public function __construct()
    {
        parent::__construct();
        $this->setId('epacei_purchase_order_view_tabs');
        $this->setDestElementId('epacei_purchase_order_view');
        $this->setTitle(Mage::helper('epacei')->__('PurchaseOrder View'));
    }

}
