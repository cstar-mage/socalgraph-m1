<?php

/**
 * Notifications Rule General Information Tab
 *
 * @package Blackbox_Notification
 */
class Blackbox_Notification_Block_Head_Rule_Edit_Tab_Main
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
        return Mage::helper('blackbox_notification')->__('Rule Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('blackbox_notification')->__('Rule Information');
    }

    /**
     * Returns status flag about this tab can be showed or not
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
        $model = Mage::registry('current_blackbox_notification_notification_rule'); /* @var Blackbox_Notification_Model_Rule $model */

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend' => Mage::helper('blackbox_notification')->__('General Information'))
        );

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', array(
                'name' => 'rule_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('blackbox_notification')->__('Rule Name'),
            'title' => Mage::helper('blackbox_notification')->__('Rule Name'),
            'required' => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('blackbox_notification')->__('Description'),
            'title' => Mage::helper('blackbox_notification')->__('Description'),
            'style' => 'height: 100px;',
        ));

        $type = $fieldset->addField('type', 'select', array(
            'name'      => 'type',
            'label'     => Mage::helper('blackbox_notification')->__('Type'),
            'title'     => Mage::helper('blackbox_notification')->__('Type'),
            'required'  => true,
            'options'    => $model->getTypes(),
        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('blackbox_notification')->__('Status'),
            'title'     => Mage::helper('blackbox_notification')->__('Status'),
            'name'      => 'is_active',
            'required' => true,
            'options'    => array(
                '1' => Mage::helper('blackbox_notification')->__('Active'),
                '0' => Mage::helper('blackbox_notification')->__('Inactive'),
            ),
        ));

        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        if (Mage::app()->isSingleStoreMode()) {
            $websiteId = Mage::app()->getStore(true)->getWebsiteId();
            $fieldset->addField('website_ids', 'hidden', array(
                'name'     => 'website_ids[]',
                'value'    => $websiteId
            ));
            $model->setWebsiteIds($websiteId);
        } else {
            $field = $fieldset->addField('website_ids', 'multiselect', array(
                'name'     => 'website_ids[]',
                'label'     => Mage::helper('blackbox_notification')->__('Websites'),
                'title'     => Mage::helper('blackbox_notification')->__('Websites'),
                'required' => true,
                'values'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm(false, true)
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }

        $fieldset->addField('customer_ids', 'multiselect', array(
            'name'     => 'customer_ids[]',
            'label'     => Mage::helper('blackbox_notification')->__('Customers'),
            'title'     => Mage::helper('blackbox_notification')->__('Customers'),
            'required' => true,
            'values'   => Mage::getSingleton('blackbox_notification/head_notification_customers_source')->getAllOptions()
        ));

        $settingsFieldset = $form->addFieldset('conditions_fieldset', array(
            'legend'=>Mage::helper('blackbox_notification')->__('Conditions')
        ));

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/rule/newConditionHtml/form/rule_actions_fieldset'));
        $settingsFieldset->setRenderer($renderer);

        $settingsFieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('blackbox_notification')->__('Apply To'),
            'title' => Mage::helper('blackbox_notification')->__('Apply To'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        $form->setValues($model->getData());

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);

        // field dependencies
        $this->setChild('form_after', $this->getLayout()->createBlock('core/template')
            ->setTemplate('blackbox/notification/type_js.phtml')
            ->setAjaxUrl($this->getUrl('*/rule/getNewTypeSettingsJson/form/rule_conditions_fieldset'))
            ->setType($type->getHtmlId())
            ->setFieldset($settingsFieldset->getHtmlId())
        );

        Mage::dispatchEvent('adminhtml_blackbox_notification_head_edit_tab_main_prepare_form', array('form' => $form));

        return parent::_prepareForm();
    }
}
