<?php
/**
 * Invoice view  comments form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_Estimate_Comments_View extends Mage_Adminhtml_Block_Template
{
    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid parent block for this block.'));
        }
        $this->setEntity($this->getParentBlock()->getSource());
        parent::_beforeToHtml();
    }

    /**
     * Prepare child blocks
     *
     * @return Blackbox_EpaceImport_Block_Adminhtml_Estimate_Invoice_Create_Items
     */
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id'      => 'submit_comment_button',
                'label'   => Mage::helper('epacei')->__('Submit Comment'),
                'class'   => 'save'
            ));
        $this->setChild('submit_button', $button);

        return parent::_prepareLayout();
    }

    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/addComment',array('id'=>$this->getEntity()->getId()));
    }

    public function canSendCommentEmail()
    {
        switch ($this->getParentType()) {
            case 'invoice':
                return Mage::helper('epacei')->canSendInvoiceCommentEmail(
                    $this->getEntity()->getEstimate()->getStore()->getId()
                );
            case 'shipment':
                return Mage::helper('epacei')->canSendShipmentCommentEmail(
                    $this->getEntity()->getEstimate()->getStore()->getId()
                );
            case 'creditmemo':
                return Mage::helper('epacei')->canSendCreditmemoCommentEmail(
                    $this->getEntity()->getEstimate()->getStore()->getId()
                );
        }

        return true;
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
