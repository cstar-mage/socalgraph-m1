<?php
/**
 * Adminhtml epacei estimates block
 */
class Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'epacei';
        $this->_controller = 'adminhtml_purchaseOrder';
        $this->_headerText = Mage::helper('epacei')->__('Purchase Orders');
        parent::__construct();
        $this->_removeButton('add');
    }

    public function getCreateUrl()
    {
        return null;//return $this->getUrl('*/epacei_estimate_create/start');
    }

}
