<?php

class Blackbox_Notification_Block_Rule_Edit_Tab_Emails_Redirect_Config_Renderer_Group extends Mage_Core_Block_Html_Select
{
    public function _toHtml()
    {
        $options = Mage::getResourceModel('customer/group_collection')->load()->toOptionArray();
        $this->addOption('default', 'Default');
        foreach ($options as $option) {
            $this->addOption($option['value'], $option['label']);
        }

        return parent::_toHtml();
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }
}