<?php

/**
 * Notifications log
 *
 * @package    Blackbox_Notification
 */

class Blackbox_Notification_Block_Log extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'log';
        $this->_blockGroup = 'blackbox_notification';
        $this->_headerText = Mage::helper('blackbox_notification')->__('Notifications Log');

        parent::__construct();

        $this->_removeButton('add');
    }
}
