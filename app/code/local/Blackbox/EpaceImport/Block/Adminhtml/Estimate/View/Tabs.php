<?php
/**
 * Estimate view tabs
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_Estimate_View_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
        Mage::throwException(Mage::helper('epacei')->__('Cannot get the estimate instance.'));
    }

    public function __construct()
    {
        parent::__construct();
        $this->setId('epacei_estimate_view_tabs');
        $this->setDestElementId('epacei_estimate_view');
        $this->setTitle(Mage::helper('epacei')->__('Estimate View'));
    }

}
