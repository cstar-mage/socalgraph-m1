<?php
/**
 * PurchaseOrder history block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_View_History extends Mage_Adminhtml_Block_Template
{
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('purchase_order_history_block').parentNode, '".$this->getSubmitUrl()."')";
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
        $statuses = $this->getPurchaseOrder()->getConfig()->getStatuses();
        return $statuses;
    }

    public function canSendCommentEmail()
    {
        return false;
    }

    /**
     * Retrieve purchase order model
     *
     * @return Blackbox_EpaceImport_Model_PurchaseOrder
     */
    public function getPurchaseOrder()
    {
        return Mage::registry('purchase_order');
    }

    public function canAddComment()
    {
        return Mage::getSingleton('admin/session')->isAllowed('epacei/purchase_order/actions/comment') &&
               $this->getPurchaseOrder()->canComment();
    }

    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/addComment', array('purchase_order_id'=>$this->getPurchaseOrder()->getId()));
    }

    /**
     * Customer Notification Applicable check method
     *
     * @param  Blackbox_EpaceImport_Model_PurchaseOrder_Status_History $history
     * @return boolean
     */
    public function isCustomerNotificationNotApplicable(Blackbox_EpaceImport_Model_PurchaseOrder_Status_History $history)
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
        return Mage::helper('adminhtml/sales')->escapeHtmlWithLinks($data, $allowedTags);
    }
}
