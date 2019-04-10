<?php
/**
 * Adminhtml epacei purchase order view plane
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_View_Form extends Mage_Adminhtml_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('blackbox/epacei/purchase_order/view/form.phtml');
    }
}
