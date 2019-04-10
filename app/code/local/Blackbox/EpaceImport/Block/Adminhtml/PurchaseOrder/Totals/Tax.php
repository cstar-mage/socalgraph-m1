<?php
/**
 * Adminhtml purchase order tax totals block
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_Totals_Tax extends Mage_Tax_Block_Sales_Order_Tax
{
    /**
     * Get full information about taxes applied to purchase order
     *
     * @return array
     */
    public function getFullTaxInfo()
    {
        /** @var $source Blackbox_EpaceImport_Model_PurchaseOrder */
        $source = $this->getPurchaseOrder();

        $taxClassAmount = array();
        if ($source instanceof Blackbox_EpaceImport_Model_PurchaseOrder) {
            $taxClassAmount = $this->_getTaxHelper()->getCalculatedTaxes($source);
        }

        return $taxClassAmount;
    }

    /**
     * Return Mage_Tax_Helper_Data instance
     *
     * @return Mage_Tax_Helper_Data
     */
    protected function _getTaxHelper()
    {
        return Mage::helper('tax');
    }

    /**
     * Display tax amount
     *
     * @return string
     */
    public function displayAmount($amount, $baseAmount)
    {
        return Mage::helper('adminhtml/sales')->displayPrices(
            $this->getSource(), $baseAmount, $amount, false, '<br />'
        );
    }

    /**
     * Get store object for process configuration settings
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore();
    }
}
