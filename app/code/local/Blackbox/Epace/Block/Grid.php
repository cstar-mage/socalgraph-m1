<?php
/**
 * The Magento Developer
 * http://themagentodeveloper.com
 *
 * @category   Blackbox
 * @package    Blackbox_Epace
 * @version    0.1.2
 */

class Blackbox_Epace_Block_Grid extends Mage_Adminhtml_Block_Widget_Grid_Container {
    public function __construct()
    {
        $this->_controller = 'adminhtml_epace';
        $this->_blockGroup = 'epace';
        //text in the admin header
        $this->_headerText = 'Epace';
        //value of the add button

        parent::__construct();

        $this->_removeButton('add');
        $this->_addButton('config', array(
            'label'     => Mage::helper('epace')->__('Config'),
            'onclick'   => 'setLocation(\'' . $this->getConfigUrl() .'\')',
            'class'     => '',
        ));
    }

    protected function getConfigUrl()
    {
        return Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/system_config/edit/section/epace');
    }
}