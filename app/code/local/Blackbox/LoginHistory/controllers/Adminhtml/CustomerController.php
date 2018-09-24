<?php
require_once(Mage::getModuleDir('controllers','Mage_Adminhtml') . DS . 'CustomerController.php');

class Blackbox_LoginHistory_Adminhtml_CustomerController extends Mage_Adminhtml_CustomerController
{
    public function loginHistoryAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }
}