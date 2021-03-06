<?php

/**
 * Approval view form
 *
 * @category   Blackbox
 * @package    Blackbox_OrderApproval
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_OrderApproval_Block_Adminhtml_Order_Approval_View_Form extends Mage_Adminhtml_Block_Sales_Order_Abstract
{
    /**
     * Retrieve Approval order
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
     * Retrieve order url
     */
    public function getOrderUrl()
    {
        return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $this->getApproval()->getOrderId()));
    }

    /**
     * Retrieve formated price
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->getApproval()->getOrder()->formatPrice($price);
    }
}
