<?php
require_once(Mage::getModuleDir('controllers','Mage_Adminhtml').DS.'IndexController.php');

class Blackbox_RolesPermissions_Adminhtml_IndexController extends Mage_Adminhtml_IndexController
{
    /**
     * Administrator logout action
     */
    public function logoutAction()
    {
        if (!Mage::helper('rolespermissions')->isEnabled()) {
            return parent::logoutAction();
        }

        /** @var $adminSession Mage_Admin_Model_Session */
        $adminSession = Mage::getSingleton('admin/session');
        $adminSession->unsetAll();
        $adminSession->getCookie()->delete($adminSession->getSessionName());

        $this->_redirect('customer/account/login');
    }

    public function loginRedirectAction()
    {
        $this->_redirect('customer/account/login');
    }
}