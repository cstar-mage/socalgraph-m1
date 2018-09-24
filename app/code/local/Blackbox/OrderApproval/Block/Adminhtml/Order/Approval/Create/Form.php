<?php

/**
 * Adminhtml approval create form
 *
 * @category   Blackbox
 * @package    Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Block_Adminhtml_Order_Approval_Create_Form extends Mage_Adminhtml_Block_Sales_Order_Abstract
{
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

    public function getRuleSelectHtml()
    {
        $select = Mage::app()->getLayout()->createBlock('adminhtml/html_select')
            ->setName('rule_id')
            ->setId('rule_id')
            ->setTitle('Select Rule')
            ->setValue($this->getApproval()->getRuleId())
            ->setOptions($this->getRulesOptions());
        return $select->getHtml();
    }

    public function getRulesOptions()
    {
        $options = array();
        foreach($this->getRules() as $rule)
        {
            $options[$rule->getId()] = $rule->getName();
        }
        return $options;
    }

    public function getRules()
    {
        return Mage::registry('approval_rules');
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('order_id' => $this->getApproval()->getOrderId()));
    }
}
