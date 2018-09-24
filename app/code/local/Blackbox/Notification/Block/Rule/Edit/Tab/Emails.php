<?php

/**
 * description
 *
 * @package    Blackbox_Notification
 */
class Blackbox_Notification_Block_Rule_Edit_Tab_Emails
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('blackbox_notification')->__('Emails');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('blackbox_notification')->__('Emails');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_blackbox_notification_rule');

        //$form = new Varien_Data_Form(array('id' => 'edit_form1', 'action' => $this->getData('action'), 'method' => 'post'));
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('products_fieldset', array(
            'legend'=>Mage::helper('blackbox_notification')->__('Specify emails settings:')
        ));

        $fieldset->addField('email_sender', 'select', array(
            'label'     => Mage::helper('blackbox_notification')->__('Email Sender'),
            'name'      => 'email_sender',
            'values'    => Mage::getModel('adminhtml/system_config_source_email_identity')->toOptionArray(),
        ));

        $fieldset->addField('email_template_id', 'select', array(
            'label'     => Mage::helper('blackbox_notification')->__('Email Template'),
            'name'      => 'email_template_id',
            'values'    => Mage::getModel('adminhtml/system_config_source_email_template')->setPath($model->getEmailTemplateNode())->toOptionArray(),
            'required'  => true
        ));

        $fieldset->addField('emails', 'text', array(
            'name' => 'emails',
            'label' => Mage::helper('blackbox_notification')->__('Emails'),
            'title' => Mage::helper('blackbox_notification')->__('Emails'),
            'comment' => 'Comma Separated'
        ));

        $fieldset->addField('copy_method', 'select', array(
            'label'     => 'Copy Method',
            'name'      => 'copy_method',
            'values'    => Mage::getSingleton('adminhtml/system_config_source_email_method')->toOptionArray()
        ));

        $fieldset->addType('redirect_config', 'Blackbox_Notification_Block_Rule_Edit_Tab_Emails_Redirect_Config_Element');

        $fieldset->addField('redirect_config', 'redirect_config', array(
            'label'     => 'Redirect Config',
            'name'      => 'redirect_config'
        ));

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
