<?php

/**
 * description
 *
 * @package    Blackbox_Notification
 */
class Blackbox_Notification_Block_Head_Rule_Edit_Tab_Content
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
        return Mage::helper('blackbox_notification')->__('Content');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('blackbox_notification')->__('Content');
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
        $model = Mage::registry('current_blackbox_notification_notification_rule');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('products_fieldset', array(
            'legend'=>Mage::helper('blackbox_notification')->__('Content:')
        ));

        $fieldset->addField('content_template', 'textarea', array(
            'name' => 'content_template',
            'label' => Mage::helper('blackbox_notification')->__('Content Template'),
            'title' => Mage::helper('blackbox_notification')->__('Content Template'),
        ));

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
