<?php

/**
 * Adminhtml estimate abstract block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_Estimate_Abstract extends Mage_Adminhtml_Block_Widget
{
    /**
     * Retrieve available estimate
     *
     * @return Blackbox_EpaceImport_Model_Estimate
     */
    public function getEstimate()
    {
        if ($this->hasEstimate()) {
            return $this->getData('estimate');
        }
        if (Mage::registry('current_estimate')) {
            return Mage::registry('current_estimate');
        }
        if (Mage::registry('estimate')) {
            return Mage::registry('estimate');
        }
        Mage::throwException(Mage::helper('epacei')->__('Cannot get estimate instance'));
    }

    public function getPriceDataObject()
    {
        $obj = $this->getData('price_data_object');
        if (is_null($obj)) {
            return $this->getEstimate();
        }
        return $obj;
    }

    public function displayPriceAttribute($code, $strong = false, $separator = '<br/>')
    {
        return $this->helper('adminhtml/epacei')->displayPriceAttribute($this->getPriceDataObject(), $code, $strong, $separator);
    }

    public function displayPrices($basePrice, $price, $strong = false, $separator = '<br/>')
    {
        return $this->helper('adminhtml/epacei')->displayPrices($this->getPriceDataObject(), $basePrice, $price, $strong, $separator);
    }

    /**
     * Retrieve estimate totals block settings
     *
     * @return array
     */
    public function getEstimateTotalData()
    {
        return array();
    }

    /**
     * Retrieve estimate info block settings
     *
     * @return array
     */
    public function getEstimateInfoData()
    {
        return array();
    }


    /**
     * Retrieve subtotal price include tax html formated content
     *
     * @param Varien_Object $item
     * @return string
     */
    public function displayShippingPriceInclTax($estimate)
    {
        $shipping = $estimate->getShippingInclTax();
        if ($shipping) {
            $baseShipping = $estimate->getBaseShippingInclTax();
        } else {
            $shipping       = $estimate->getShippingAmount()+$estimate->getShippingTaxAmount();
            $baseShipping   = $estimate->getBaseShippingAmount()+$estimate->getBaseShippingTaxAmount();
        }
        return $this->displayPrices($baseShipping, $shipping, false, ' ');
    }
}
