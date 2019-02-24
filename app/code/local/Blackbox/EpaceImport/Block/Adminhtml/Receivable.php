<?php
/**
 * Adminhtml epacei receivables block
 */
class Blackbox_EpaceImport_Block_Adminhtml_Receivable extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'epacei';
        $this->_controller = 'adminhtml_receivable';
        $this->_headerText = Mage::helper('epacei')->__('Receivables');
        $this->_addButtonLabel = Mage::helper('epacei')->__('Create New Receivable');
        parent::__construct();
//        if (!Mage::getSingleton('admin/session')->isAllowed('epacei/receivable/actions/create')) {
        $this->_removeButton('add');
//        }
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/epace_receivable_create/start');
    }

}
