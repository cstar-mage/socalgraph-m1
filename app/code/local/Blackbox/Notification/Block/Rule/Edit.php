<?php

/**
 * Notifications rule edit form block
 */

class Blackbox_Notification_Block_Rule_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
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
        $this->_controller = 'rule';

        parent::__construct();

        $this->_addButton('save_and_continue_edit', array(
            'class'   => 'save',
            'label'   => Mage::helper('blackbox_notification')->__('Save and Continue Edit'),
            'onclick' => 'editForm.submit($(\'edit_form\').action + \'back/edit/\')',
        ), 10);
    }

    /**
     * Getter for form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $rule = Mage::registry('current_blackbox_notification_rule');
        if ($rule->getRuleId()) {
            return Mage::helper('blackbox_notification')->__("Edit Rule '%s'", $this->escapeHtml($rule->getName()));
        }
        else {
            return Mage::helper('blackbox_notification')->__('New Rule');
        }
    }

    /**
     * Retrieve products JSON
     *
     * @return string
     */
    public function getProductsJson()
    {
        return '{}';
    }
}
