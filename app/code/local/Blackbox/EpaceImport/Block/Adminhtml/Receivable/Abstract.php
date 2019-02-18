<?php

/**
 * Adminhtml receivable abstract block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_Receivable_Abstract extends Mage_Adminhtml_Block_Widget
{
    /**
     * Retrieve available receivable
     *
     * @return Blackbox_EpaceImport_Model_Receivable
     */
    public function getReceivable()
    {
        if ($this->hasReceivable()) {
            return $this->getData('receivable');
        }
        if (Mage::registry('current_receivable')) {
            return Mage::registry('current_receivable');
        }
        if (Mage::registry('receivable')) {
            return Mage::registry('receivable');
        }
        Mage::throwException(Mage::helper('epacei')->__('Cannot get receivable instance'));
    }

    public function getPriceDataObject()
    {
        $obj = $this->getData('price_data_object');
        if (is_null($obj)) {
            return $this->getReceivable();
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
     * Retrieve receivable totals block settings
     *
     * @return array
     */
    public function getReceivableTotalData()
    {
        return array();
    }

    /**
     * Retrieve receivable info block settings
     *
     * @return array
     */
    public function getReceivableInfoData()
    {
        return array();
    }


    /**
     * Retrieve subtotal price include tax html formated content
     *
     * @param Varien_Object $item
     * @return string
     */
    public function displayShippingPriceInclTax($receivable)
    {
        $shipping = $receivable->getShippingInclTax();
        if ($shipping) {
            $baseShipping = $receivable->getBaseShippingInclTax();
        } else {
            $shipping       = $receivable->getShippingAmount()+$receivable->getShippingTaxAmount();
            $baseShipping   = $receivable->getBaseShippingAmount()+$receivable->getBaseShippingTaxAmount();
        }
        return $this->displayPrices($baseShipping, $shipping, false, ' ');
    }
}
