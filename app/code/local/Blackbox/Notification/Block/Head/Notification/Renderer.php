<?php

/**
 * @method Blackbox_Notification_Model_Notification getItem()
 * @method $this setItem(Blackbox_Notification_Model_Notification $item)
 *
 * Class Blackbox_Notification_Block_Head_Notification_Renderer
 */
class Blackbox_Notification_Block_Head_Notification_Renderer extends Mage_Core_Block_Template
{
    public function processTemplate($text)
    {
        /** @var Blackbox_Notification_Model_Head_Notification_Filter_Template $processor */
        $processor = Mage::getModel('blackbox_notification/head_notification_filter_template');
        return $processor->filter($text);
    }
}