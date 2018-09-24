<?php

/**
 * Notifications rule edit form block
 */

class Blackbox_Notification_Block_Log_View extends Mage_Adminhtml_Block_Widget_Form_Container
{

    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Continue" button
     */
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'blackbox_notification';
        $this->_controller = 'log';
        $this->_mode = 'view';

        parent::__construct();

        $this->_removeButton('save');
        $this->_removeButton('reset');
        $this->_removeButton('delete');
    }

    /**
     * Getter for form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('blackbox_notification')->__('Notification Event Details');
    }
}
