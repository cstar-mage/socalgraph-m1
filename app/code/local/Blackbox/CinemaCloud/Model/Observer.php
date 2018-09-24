<?php

class Blackbox_CinemaCloud_Model_Observer
{
    public function initRouters($observer)
    {
        /** @var Mage_Core_Controller_Varien_Front $front */
        $front = $observer->getFront();
        // create link from standard to custom router as a workaround
        // for Mage_Core_Controller_Varien_Front::getRouterByRoute and Mage_Core_Controller_Varien_Front::getRouterByFrontName
        $front->addRouter('standard', $front->getRouter('cinemacloud'));
    }

    public function controllerActionPredispatch($observer)
    {
        /** @var Mage_Core_Controller_Varien_Action $controller */
        $controller = $observer->getControllerAction();
        if ($controller->getRequest()->getModuleName() == 'api') {
            return;
        }
        $customerSession = $this->_getCustomerSession();
        if (Mage::app()->getStore()->isAdmin()) {
            if ($customerSession->isLoggedIn()) {
                if ($this->_getHelper()->hasAccessToAdminDashboard($customerSession->getCustomer())) {
                    return;
                }
                $this->logoutWithoutChangeSession();
            }
            //$this->redirectToLogin($controller);
        } else if ($customerSession->isLoggedIn()) {
            $this->redirectToAdminDashboard($controller);
        }
    }

    protected function redirectToLogin(Mage_Core_Controller_Varien_Action $controller)
    {
        $controller->getResponse()->setRedirect(Mage::getUrl('customer/account/login'))->sendResponse();
        die;
    }

    protected function redirectToAdminDashboard(Mage_Core_Controller_Varien_Action $controller)
    {
        $controller->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl('adminhtml/index/index'))->sendResponse();
        die;
    }

    protected function logoutWithoutChangeSession()
    {
        $this->_getCustomerSession()->setId(null)->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        Mage::getSingleton('core/session')->renewFormKey();
    }

    /**
     * @return Blackbox_RolesPermissions_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('rolespermissions');
    }

    /**
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }
}