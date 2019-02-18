<?php
/**
 * Receivable view tabs
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_Receivable_View_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
        Mage::throwException(Mage::helper('epacei')->__('Cannot get the receivable instance.'));
    }

    public function __construct()
    {
        parent::__construct();
        $this->setId('epacei_receivable_view_tabs');
        $this->setDestElementId('epacei_receivable_view');
        $this->setTitle(Mage::helper('epacei')->__('Receivable View'));
    }

}
