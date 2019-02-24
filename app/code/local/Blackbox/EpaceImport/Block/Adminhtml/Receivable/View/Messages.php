<?php
/**
 * Receivable view messages
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_Receivable_View_Messages extends Mage_Adminhtml_Block_Messages
{

    protected function _getReceivable()
    {
        return Mage::registry('epacei_receivable');
    }

    public function _prepareLayout()
    {
        /**
         * Check customer existing
         */
        $customer = Mage::getModel('customer/customer')->load($this->_getReceivable()->getCustomerId());

        /**
         * Check Item products existing
         */
        $productIds = array();
        foreach ($this->_getReceivable()->getAllItems() as $item) {
            $productIds[] = $item->getProductId();
        }

        return parent::_prepareLayout();
    }

}
