<?php

/**
 * Adminhtml sales purchase order item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_Items_Renderer_Default extends Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_Items_Abstract
{
    public function getItem()
    {
        return $this->_getData('item');//->getPurchaseOrderItem();
    }
}
