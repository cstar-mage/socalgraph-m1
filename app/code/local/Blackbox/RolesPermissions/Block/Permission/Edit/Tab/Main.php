<?php

/**
 * Permission Rule General Information Tab
 *
 * @package Blackbox_RolesPermissions
 */
class Blackbox_RolesPermissions_Block_Permission_Edit_Tab_Main
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
        return Mage::helper('rolespermissions')->__('Rule Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('rolespermissions')->__('Rule Information');
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
        $model = Mage::registry('current_permission_rule');

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend' => Mage::helper('rolespermissions')->__('General Information'))
        );

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', array(
                'name' => 'rule_id',
            ));
        }

        $fieldset->addField('product_ids', 'hidden', array(
            'name' => 'product_ids',
        ));

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('rolespermissions')->__('Rule Name'),
            'title' => Mage::helper('rolespermissions')->__('Rule Name'),
            'required' => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('rolespermissions')->__('Description'),
            'title' => Mage::helper('rolespermissions')->__('Description'),
            'style' => 'height: 100px;',
        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('rolespermissions')->__('Status'),
            'title'     => Mage::helper('rolespermissions')->__('Status'),
            'name'      => 'is_active',
            'required' => true,
            'options'    => array(
                '1' => Mage::helper('rolespermissions')->__('Active'),
                '0' => Mage::helper('rolespermissions')->__('Inactive'),
            ),
        ));

        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        $field = $fieldset->addField('website_ids', 'multiselect', array(
            'name'     => 'website_ids[]',
            'label'     => Mage::helper('rolespermissions')->__('Websites'),
            'title'     => Mage::helper('rolespermissions')->__('Websites'),
            'required' => true,
            'values'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm(false, true)
        ));
        $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
        $field->setRenderer($renderer);

        $scope = $fieldset->addField('scope', 'select', array(
            'name'      => 'scope',
            'label'     => Mage::helper('rolespermissions')->__('Scope'),
            'title'     => Mage::helper('rolespermissions')->__('Scope'),
            'required'  => true,
            'values'    => Mage::helper('rolespermissions')->getScopeOptionsArray(),
        ));

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/permission/newActionHtml/form/rule_actions_fieldset'));

        $fieldset->addField('simple_action', 'select', array(
            'label'     => Mage::helper('rolespermissions')->__('Action'),
            'name'      => 'simple_action',
            'options'    => array(
                'deny' => Mage::helper('rolespermissions')->__('Deny'),
                'allow' => Mage::helper('rolespermissions')->__('Allow')
            ),
        ));

        $fieldset->addField('stop_rules_processing', 'select', array(
            'label'     => Mage::helper('catalogrule')->__('Stop Further Rules Processing'),
            'title'     => Mage::helper('catalogrule')->__('Stop Further Rules Processing'),
            'name'      => 'stop_rules_processing',
            'options'   => array(
                '1' => Mage::helper('catalogrule')->__('Yes'),
                '0' => Mage::helper('catalogrule')->__('No'),
            ),
        ));

        $actionFieldset = $form->addFieldset('actions_fieldset', array(
            'legend'=>Mage::helper('salesrule')->__('Apply the rule only to items matching the following conditions (leave blank for all items)')
        ))->setRenderer($renderer);

        $actionFieldset->addField('actions', 'text', array(
            'name' => 'actions',
            'label' => Mage::helper('rolespermissions')->__('Apply To'),
            'title' => Mage::helper('rolespermissions')->__('Apply To'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/actions'));

        $fieldset->addField('sort_order', 'text', array(
            'name' => 'sort_order',
            'label' => Mage::helper('rolespermissions')->__('Priority')
        ));

        $form->setValues($model->getData());

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        //$form->setUseContainer(true);

        $this->setForm($form);

        // field dependencies
        $this->setChild('form_after', $this->getLayout()->createBlock('core/template')
            ->setTemplate('blackbox/rolespermissions/scope_js.phtml')
            ->setAjaxUrl($this->getUrl('*/permission/newActionBlockHtml/form/rule_actions_fieldset'))
            ->setScope($scope->getHtmlId())
            ->setFieldset($actionFieldset->getHtmlId())
        );

        Mage::dispatchEvent('adminhtml_permission_edit_tab_main_prepare_form', array('form' => $form));

        return parent::_prepareForm();
    }
}
