<?php

/**
 * Notifications rules
 *
 * @package    Blackbox_Notification
 */

class Blackbox_Notification_Block_Rule extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'rule';
        $this->_blockGroup = 'blackbox_notification';
        $this->_headerText = Mage::helper('blackbox_notification')->__('Email Notifications');
        $this->_addButtonLabel = Mage::helper('blackbox_notification')->__('Add New Rule');

        parent::__construct();
    }
}
