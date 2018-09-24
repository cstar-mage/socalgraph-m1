<?php

class Cmsmart_Onlinedesign_Block_Adminhtml_System_Config_Form_Field_Jscolor
    extends Varien_Data_Form_Element_Text
{
    public function _toHtml()
    {
        $options = Mage::getSingleton('adminhtml/system_config_source_country')
            ->toOptionArray();
        foreach ($options as $option) {
            $this->addOption($option['value'], $option['label']);
        }
 
        return parent::_toHtml();
    }
}
