<?php

/**
 * Grid block class
 *
 * Class Wizkunde_WebSSO_Block_Adminhtml_Idp
 */
class Wizkunde_WebSSO_Block_Adminhtml_Idp extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor to define the block information
     */
    public function __construct()
    {
        $this->_blockGroup = 'websso';
        $this->_controller = 'adminhtml_idp';
        $this->_headerText = $this->__('Identity Providers');

        parent::__construct();
    }
}