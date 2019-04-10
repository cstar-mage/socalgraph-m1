<?php

/**
 * Sales Order items name column renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_Items_Column_Name_Grouped extends Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_Items_Column_Name
{
    /**
     * Prepare item html
     *
     * This method uses renderer for real product type
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getItem()->getPurchaseOrderItem()) {
            $item = $this->getItem()->getPurchaseOrderItem();
        } else {
            $item = $this->getItem();
        }
        if ($productType = $item->getRealProductType()) {
            $renderer = $this->getRenderedBlock()->getColumnHtml($this->getItem(), $productType);
            return $renderer;
        }
        return parent::_toHtml();
    }
}
?>
