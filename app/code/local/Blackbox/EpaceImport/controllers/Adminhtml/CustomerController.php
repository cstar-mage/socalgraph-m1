<?php
require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml').DS.'CustomerController.php';

class Blackbox_EpaceImport_Adminhtml_CustomerController extends Mage_Adminhtml_CustomerController
{
    public function estimatesAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function shipmentsAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }
}
