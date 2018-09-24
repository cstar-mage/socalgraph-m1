<?php

/**
 * @package    Blackbox_Notification
 */
class Blackbox_Notification_Block_Log_View_Form extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('blackbox/notification/log/view/form.phtml');
    }

    /**
     * @return Blackbox_Notification_Model_Log
     */
    public function getLog()
    {
        return Mage::registry('current_blackbox_notification_log');
    }

    public function getType()
    {
        return Mage::getSingleton('blackbox_notification/rule')->getTypes()[$this->getLog()->getType()];
    }
}
