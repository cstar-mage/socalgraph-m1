<?php

/**
 * Adminhtml purchase order abstract block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_Abstract extends Mage_Adminhtml_Block_Widget
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
        Mage::throwException(Mage::helper('epacei')->__('Cannot get purchase_order instance'));
    }

    public function getPriceDataObject()
    {
        $obj = $this->getData('price_data_object');
        if (is_null($obj)) {
            return $this->getPurchaseOrder();
        }
        return $obj;
    }

    public function displayPriceAttribute($code, $strong = false, $separator = '<br/>')
    {
        return $this->helper('adminhtml/sales')->displayPriceAttribute($this->getPriceDataObject(), $code, $strong, $separator);
    }

    public function displayPrices($basePrice, $price, $strong = false, $separator = '<br/>')
    {
        return $this->helper('adminhtml/sales')->displayPrices($this->getPriceDataObject(), $basePrice, $price, $strong, $separator);
    }

    /**
     * Retrieve purchase order totals block settings
     *
     * @return array
     */
    public function getPurchaseOrderTotalData()
    {
        return array();
    }

    /**
     * Retrieve purchase order info block settings
     *
     * @return array
     */
    public function getPurchaseOrderInfoData()
    {
        return array();
    }


    /**
     * Retrieve subtotal price include tax html formated content
     *
     * @param Varien_Object $item
     * @return string
     */
    public function displayShippingPriceInclTax($purchaseOrder)
    {
        $shipping = $purchaseOrder->getShippingInclTax();
        if ($shipping) {
            $baseShipping = $purchaseOrder->getBaseShippingInclTax();
        } else {
            $shipping       = $purchaseOrder->getShippingAmount()+$purchaseOrder->getShippingTaxAmount();
            $baseShipping   = $purchaseOrder->getBaseShippingAmount()+$purchaseOrder->getBaseShippingTaxAmount();
        }
        return $this->displayPrices($baseShipping, $shipping, false, ' ');
    }
}
