<?php

/**
 * Adminhtml disapproval create form
 *
 * @category   Blackbox
 * @package    Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Block_Adminhtml_Order_Disapproval_View_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('rule_form');
        $this->setTitle(Mage::helper('order_approval')->__('Select Rule'));

        $this->setAction($this->getSaveUrl());
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => '', 'method' => ''));
        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('view_fieldset', array('legend' => Mage::helper('order_approval')->__('Select Rule')));

        $fieldset->addField('rule', 'text', array(
            'label'     => Mage::helper('order_approval')->__('Rule'),
            'title'     => Mage::helper('order_approval')->__('Rule'),
            'name'      => 'rule_id',
            'required'  => true,
            'value'     => $this->getDisapproval()->getRule()->getName(),
            'readonly'  => true
        ));

        $fieldset->addField('comment', 'textarea', array(
            'label'     => Mage::helper('order_approval')->__('Comment'),
            'title'     => Mage::helper('order_approval')->__('Comment'),
            'name'      => 'comment',
            'required'  => true,
            'value'     => $this->getDisapproval()->getComment(),
            'readonly'  => true
        ));

        //$this->getDisapproval()->getComment();
        //$form->setValues($this->getDisapproval()->getData());

        return parent::_prepareForm();
    }

    /**
     * Retrieve disapproval order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getDisapproval()->getOrder();
    }

    /**
     * Retrieve source
     *
     * @return Blackbox_OrderApproval_Model_Disapproval
     */
    public function getSource()
    {
        return $this->getDisapproval();
    }

    public function getRules()
    {
        return Mage::helper('order_approval')->getAvailableRules($this->getOrder());
    }

    /**
     * Retrieve disapproval model instance
     *
     * @return Blackbox_OrderApproval_Model_Disapproval
     */
    public function getDisapproval()
    {
        return Mage::registry('current_disapproval');
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('order_id' => $this->getDisapproval()->getOrderId()));
    }
}
