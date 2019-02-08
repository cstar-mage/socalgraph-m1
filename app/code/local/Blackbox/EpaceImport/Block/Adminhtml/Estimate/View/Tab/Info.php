<?php
/**
 * Estimate information tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_Estimate_View_Tab_Info
    extends Blackbox_EpaceImport_Block_Adminhtml_Estimate_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Retrieve estimate model instance
     *
     * @return Blackbox_EpaceImport_Model_Estimate
     */
    public function getEstimate()
    {
        return Mage::registry('current_estimate');
    }

    /**
     * Retrieve source model instance
     *
     * @return Blackbox_EpaceImport_Model_Estimate
     */
    public function getSource()
    {
        return $this->getEstimate();
    }

    /**
     * Retrieve estimate totals block settings
     *
     * @return array
     */
    public function getEstimateTotalData()
    {
        return array(
            'can_display_total_due'      => true,
            'can_display_total_paid'     => true,
            'can_display_total_refunded' => true,
        );
    }

    public function getEstimateInfoData()
    {
        return array(
            'no_use_estimate_link' => true,
        );
    }

    public function getTrackingHtml()
    {
        return $this->getChildHtml('estimate_tracking');
    }

    public function getItemsHtml()
    {
        return $this->getChildHtml('estimate_items');
    }

    public function getViewUrl($estimateId)
    {
        return $this->getUrl('*/*/*', array('estimate_id'=>$estimateId));
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('epacei')->__('Information');
    }

    public function getTabTitle()
    {
        return Mage::helper('epacei')->__('Estimate Information');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
