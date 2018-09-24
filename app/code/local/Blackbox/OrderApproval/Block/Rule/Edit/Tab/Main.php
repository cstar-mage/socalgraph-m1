<?php

/**
 * Order Approval Rule General Information Tab
 *
 * @package Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Block_Rule_Edit_Tab_Main
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
        return Mage::helper('order_approval')->__('Rule Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('order_approval')->__('Rule Information');
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
        $model = Mage::registry('current_order_approval_rule');

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend' => Mage::helper('order_approval')->__('General Information'))
        );

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', array(
                'name' => 'rule_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('order_approval')->__('Rule Name'),
            'title' => Mage::helper('order_approval')->__('Rule Name'),
            'required' => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('order_approval')->__('Description'),
            'title' => Mage::helper('order_approval')->__('Description'),
            'style' => 'height: 100px;',
        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('order_approval')->__('Status'),
            'title'     => Mage::helper('order_approval')->__('Status'),
            'name'      => 'is_active',
            'required' => true,
            'options'    => array(
                '1' => Mage::helper('order_approval')->__('Active'),
                '0' => Mage::helper('order_approval')->__('Inactive'),
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
                'label'     => Mage::helper('order_approval')->__('Websites'),
                'title'     => Mage::helper('order_approval')->__('Websites'),
                'required' => true,
                'values'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm(false, true)
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/rule/newActionHtml/form/rule_actions_fieldset'));

        $actionFieldset = $form->addFieldset('actions_fieldset', array(
            'legend'=>Mage::helper('salesrule')->__('Approve orders matching the following conditions (leave blank for all orders)')
        ))->setRenderer($renderer);

        $actionFieldset->addField('actions', 'text', array(
            'name' => 'actions',
            'label' => Mage::helper('order_approval')->__('Apply To'),
            'title' => Mage::helper('order_approval')->__('Apply To'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/actions'));

        $form->setValues($model->getData());

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);

        Mage::dispatchEvent('adminhtml_order_approval_edit_tab_main_prepare_form', array('form' => $form));

        return parent::_prepareForm();
    }
}
