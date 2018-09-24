<?php

class Blackbox_Notification_Block_Rule_Edit_Tab_Emails_Redirect_Config_Element extends Varien_Data_Form_Element_Abstract
{
    /** @var  Blackbox_Notification_Block_Rule_Edit_Tab_Emails_Redirect_Config */
    protected $_element;

    protected function _construct()
    {
        parent::_construct();
        $this->_element = Mage::app()->getLayout()->createBlock('blackbox_notification/rule_edit_tab_emails_redirect_config');// new Blackbox_Notification_Block_Rule_Edit_Tab_Emails_Redirect_Config();
    }

    public function getElementHtml()
    {
        $this->_element->setHtmlId($this->getHtmlId());
        return $this->_element->render($this);
    }
}