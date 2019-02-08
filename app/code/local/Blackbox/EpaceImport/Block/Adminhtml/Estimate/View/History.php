<?php
/**
 * Estimate history block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_Estimate_View_History extends Mage_Adminhtml_Block_Template
{
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('estimate_history_block').parentNode, '".$this->getSubmitUrl()."')";
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'   => Mage::helper('epacei')->__('Submit Comment'),
                'class'   => 'save',
                'onclick' => $onclick
            ));
        $this->setChild('submit_button', $button);
        return parent::_prepareLayout();
    }

    public function getStatuses()
    {
        $statuses = $this->getEstimate()->getConfig()->getStatuses();
        return $statuses;
    }

    public function canSendCommentEmail()
    {
        return false;
    }

    /**
     * Retrieve estimate model
     *
     * @return Blackbox_EpaceImport_Model_Estimate
     */
    public function getEstimate()
    {
        return Mage::registry('epacei_estimate');
    }

    public function canAddComment()
    {
        return Mage::getSingleton('admin/session')->isAllowed('epacei/estimate/actions/comment') &&
               $this->getEstimate()->canComment();
    }

    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/addComment', array('estimate_id'=>$this->getEstimate()->getId()));
    }

    /**
     * Customer Notification Applicable check method
     *
     * @param  Blackbox_EpaceImport_Model_Estimate_Status_History $history
     * @return boolean
     */
    public function isCustomerNotificationNotApplicable(Blackbox_EpaceImport_Model_Estimate_Status_History $history)
    {
        return $history->isCustomerNotificationNotApplicable();
    }

    /**
     * Replace links in string
     *
     * @param array|string $data
     * @param null|array $allowedTags
     * @return string
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        return Mage::helper('adminhtml/epacei')->escapeHtmlWithLinks($data, $allowedTags);
    }
}
