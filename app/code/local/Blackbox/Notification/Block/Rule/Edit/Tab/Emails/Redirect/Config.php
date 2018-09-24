<?php

class Blackbox_Notification_Block_Rule_Edit_Tab_Emails_Redirect_Config extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_groupRenderer;

    public function _prepareToRender()
    {
        $this->addColumn('group_id', array(
            'label' => Mage::helper('blackbox_notification')->__('Group'),
            'renderer' => $this->_getGroupRenderer(),
        ));
        $this->addColumn('url', array(
            'label' => Mage::helper('blackbox_notification')->__('Url template'),
            'style' => 'width:500px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('blackbox_notification')->__('Add');
    }

    /**
     * @return Blackbox_Notification_Block_Rule_Edit_Tab_Emails_Redirect_Config_Renderer_Group
     */
    protected function _getGroupRenderer()
    {
        if (!$this->_groupRenderer) {
            $this->_groupRenderer = $this->getLayout()->createBlock(
                'blackbox_notification/rule_edit_tab_emails_redirect_config_renderer_group', '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_groupRenderer;
    }

    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getGroupRenderer()
                ->calcOptionHash($row->getData('group_id')),
            'selected="selected"'
        );
    }
}