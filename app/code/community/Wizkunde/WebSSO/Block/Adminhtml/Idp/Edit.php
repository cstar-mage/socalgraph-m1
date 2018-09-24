<?php

/**
 * Class Wizkunde_WebSSO_Block_Adminhtml_Idp_Edit
 */
class Wizkunde_WebSSO_Block_Adminhtml_Idp_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     */
    public function __construct()
    {
        $this->_blockGroup = 'websso';
        $this->_controller = 'adminhtml_idp';

        parent::__construct();

        $this->_updateButton('save', 'label', $this->__('Save Identity Provider'));
        $this->_updateButton('delete', 'label', $this->__('Delete Identity Provider'));
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('websso')->getId()) {
            return $this->__('Edit Identity Provider');
        }
        else {
            return $this->__('New Identity Provider');
        }
    }
}