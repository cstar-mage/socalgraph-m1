<?php
/**
 * PurchaseOrder information tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_View_Tab_Info
    extends Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Retrieve purchase order model instance
     *
     * @return Blackbox_EpaceImport_Model_PurchaseOrder
     */
    public function getPurchaseOrder()
    {
        return Mage::registry('current_purchase_order');
    }

    /**
     * Retrieve source model instance
     *
     * @return Blackbox_EpaceImport_Model_PurchaseOrder
     */
    public function getSource()
    {
        return $this->getPurchaseOrder();
    }

    /**
     * Retrieve purchase order totals block settings
     *
     * @return array
     */
    public function getPurchaseOrderTotalData()
    {
        return array(
            'can_display_total_due'      => true,
            'can_display_total_paid'     => true,
            'can_display_total_refunded' => true,
        );
    }

    public function getPurchaseOrderInfoData()
    {
        return array(
            'no_use_purchase_order_link' => true,
        );
    }

    public function getTrackingHtml()
    {
        return $this->getChildHtml('purchase_order_tracking');
    }

    public function getItemsHtml()
    {
        return $this->getChildHtml('purchase_order_items');
    }

    public function getViewUrl($purchaseOrderId)
    {
        return $this->getUrl('*/*/*', array('purchase_order_id'=>$purchaseOrderId));
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('epacei')->__('Information');
    }

    public function getTabTitle()
    {
        return Mage::helper('epacei')->__('PurchaseOrder Information');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
