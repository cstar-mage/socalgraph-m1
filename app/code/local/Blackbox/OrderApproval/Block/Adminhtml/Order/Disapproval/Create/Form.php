<?php

/**
 * Adminhtml disapproval create form
 *
 * @category   Blackbox
 * @package    Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Block_Adminhtml_Order_Disapproval_Create_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('rule_form');
        $this->setTitle(Mage::helper('order_approval')->__('Select Rule'));
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));
        $form->setUseContainer(true);
        $this->setForm($form);

        $options = array();
        foreach($this->getRules() as $rule)
        {
            $options[$rule->getId()] = $rule->getName();
        }

        $fieldset = $form->addFieldset('rule', array('legend' => Mage::helper('order_approval')->__('Select Rule')));

        $fieldset->addField('rule_id', 'select', array(
            'label'     => Mage::helper('order_approval')->__('Rule'),
            'title'     => Mage::helper('order_approval')->__('Rule'),
            'name'      => 'rule_id',
            'required' => true,
            'options'    => $options,
        ));

        $fieldset->addField('comment', 'textarea', array(
            'label'     => Mage::helper('order_approval')->__('Comment'),
            'title'     => Mage::helper('order_approval')->__('Comment'),
            'name'      => 'comment',
            'required' => true,
        ));

        $this->getDisapproval()->getComment();
        $form->setValues($this->getDisapproval()->getData());

        $fieldset->addField('submit', 'submit', array(
            'label'     => '',
            'value'  => 'Submit',
            'class' => 'form-button',
            'tabindex' => 1
        ));

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
}
