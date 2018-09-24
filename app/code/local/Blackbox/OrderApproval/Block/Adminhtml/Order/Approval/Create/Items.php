<?php

/**
 * Adminhtml approval items grid
 *
 * @category   Blackbox
 * @package    Blackbox_OrderApproval
 */

class Blackbox_OrderApproval_Block_Adminhtml_Order_Approval_Create_Items extends Mage_Adminhtml_Block_Sales_Items_Abstract
{
    protected $_disableSubmitButton = false;

    /**
     * Prepare child blocks
     *
     * @return Blackbox_OrderApproval_Block_Adminhtml_Order_Approval_Create_Items
     */
    protected function _beforeToHtml()
    {
        $onclick = "submitAndReloadArea($('approval_item_container'),'".$this->getUpdateUrl()."')";
        $this->setChild(
            'update_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'class'     => 'update-button',
                'label'     => Mage::helper('sales')->__('Update Qty\'s'),
                'onclick'   => $onclick,
            ))
        );
        $this->_disableSubmitButton = true;
        $_submitButtonClass = ' disabled';
        foreach ($this->getApproval()->getAllItems() as $item) {
            /**
             * @see bug #14839
             */
            if ($item->getQty()/* || $this->getSource()->getData('base_grand_total')*/) {
                $this->_disableSubmitButton = false;
                $_submitButtonClass = '';
                break;
            }
        }
        $_submitLabel = Mage::helper('sales')->__('Submit Approval');

        $this->setChild(
            'submit_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => $_submitLabel,
                'class'     => 'save submit-button' . $_submitButtonClass,
                'onclick'   => 'disableElements(\'submit-button\');$(\'edit_form\').submit()',
                'disabled'  => $this->_disableSubmitButton
            ))
        );

        return parent::_prepareLayout();
    }

    /**
     * Get is submit button disabled or not
     *
     * @return boolean
     */
    public function getDisableSubmitButton()
    {
        return $this->_disableSubmitButton;
    }

    /**
     * Retrieve approval order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getApproval()->getOrder();
    }

    /**
     * Retrieve source
     *
     * @return Blackbox_OrderApproval_Model_Approval
     */
    public function getSource()
    {
        return $this->getApproval();
    }

    /**
     * Retrieve approval model instance
     *
     * @return Blackbox_OrderApproval_Model_Approval
     */
    public function getApproval()
    {
        return Mage::registry('current_approval');
    }

    /**
     * Retrieve order totals block settings
     *
     * @return array
     */
    public function getOrderTotalData()
    {
        return array();
    }

    /**
     * Retrieve order totalbar block data
     *
     * @return array
     */
    public function getOrderTotalbarData()
    {
        $totalbarData = array();
        $this->setPriceDataObject($this->getApproval()->getOrder());
        $totalbarData[] = array(Mage::helper('sales')->__('Paid Amount'), $this->displayPriceAttribute('amount_paid'), false);
        $totalbarData[] = array(Mage::helper('sales')->__('Refund Amount'), $this->displayPriceAttribute('amount_refunded'), false);
        $totalbarData[] = array(Mage::helper('sales')->__('Shipping Amount'), $this->displayPriceAttribute('shipping_captured'), false);
        $totalbarData[] = array(Mage::helper('sales')->__('Shipping Refund'), $this->displayPriceAttribute('shipping_refunded'), false);
        $totalbarData[] = array(Mage::helper('sales')->__('Order Grand Total'), $this->displayPriceAttribute('grand_total'), true);

        return $totalbarData;
    }

    public function formatPrice($price)
    {
        return $this->getApproval()->getOrder()->formatPrice($price);
    }

    public function getUpdateButtonHtml()
    {
        return $this->getChildHtml('update_button');
    }

    public function getUpdateUrl()
    {
        return $this->getUrl('*/*/updateQty', array('order_id'=>$this->getApproval()->getOrderId()));
    }

    public function canEditQty()
    {
        return true;
    }

    public function canSendApprovalEmail()
    {
        return Mage::helper('order_approval')->canSendNewApprovalEmail($this->getOrder()->getStore()->getId());
    }
}
