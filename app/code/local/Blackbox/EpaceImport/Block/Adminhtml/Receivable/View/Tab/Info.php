<?php
/**
 * Receivable information tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_Receivable_View_Tab_Info
    extends Blackbox_EpaceImport_Block_Adminhtml_Receivable_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Retrieve receivable model instance
     *
     * @return Blackbox_EpaceImport_Model_Receivable
     */
    public function getReceivable()
    {
        return Mage::registry('current_receivable');
    }

    /**
     * Retrieve source model instance
     *
     * @return Blackbox_EpaceImport_Model_Receivable
     */
    public function getSource()
    {
        return $this->getReceivable();
    }

    /**
     * Retrieve receivable totals block settings
     *
     * @return array
     */
    public function getReceivableTotalData()
    {
        return array(
            'can_display_total_due'      => true,
            'can_display_total_paid'     => true,
            'can_display_total_refunded' => true,
        );
    }

    public function getReceivableInfoData()
    {
        return array(
            'no_use_receivable_link' => true,
        );
    }

    public function getTrackingHtml()
    {
        return $this->getChildHtml('receivable_tracking');
    }

    public function getItemsHtml()
    {
        return $this->getChildHtml('receivable_items');
    }

    public function getViewUrl($receivableId)
    {
        return $this->getUrl('*/*/*', array('receivable_id'=>$receivableId));
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
        return Mage::helper('epacei')->__('Receivable Information');
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
