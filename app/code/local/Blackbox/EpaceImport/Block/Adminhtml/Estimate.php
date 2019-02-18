<?php
/**
 * Adminhtml epacei estimates block
 */
class Blackbox_EpaceImport_Block_Adminhtml_Estimate extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'epacei';
        $this->_controller = 'adminhtml_estimate';
        $this->_headerText = Mage::helper('epacei')->__('Estimates');
        $this->_addButtonLabel = Mage::helper('epacei')->__('Create New Estimate');
        parent::__construct();
//        if (!Mage::getSingleton('admin/session')->isAllowed('epacei/estimate/actions/create')) {
            $this->_removeButton('add');
//        }
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/epacei_estimate_create/start');
    }

}
