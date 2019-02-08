<?php
/**
 * Adminhtml estimate items grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_Estimate_View_Items extends Blackbox_EpaceImport_Block_Adminhtml_Estimate_Items_Abstract
{
    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid parent block for this block'));
        }
        $this->setEstimate($this->getParentBlock()->getEstimate());
        parent::_beforeToHtml();
    }

    /**
     * Retrieve estimate items collection
     *
     * @return unknown
     */
    public function getItemsCollection()
    {
        return $this->getEstimate()->getItemsCollection();
    }
}
