<?php

class Blackbox_CinemaCloud_Redirect_RedirectController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        $this->setFlag('', 'no_cc_admin_redirect', true);
        return parent::preDispatch();
    }

    public function shipmentAction()
    {
        $cs = $this->getCustomerSession();
        if (!$cs->isLoggedIn()) {
            $cs->setBeforeAuthUrl(Mage::helper('core/url')->getCurrentUrl());
            $this->_redirect('customer/account/login');
            return;
        }

        $customer = $cs->getCustomer();
        $as = $this->getAdminSession();
        if (!$as->isLoggedIn() || $as->getUser()->getCustomerId() != $customer->getId()) {
            if (!$as->getAcl()) {
                $as->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
            }
            $allow = !Mage::getSingleton('rolespermissions/validator')
                ->init($customer->getWebsiteId(), Blackbox_RolesPermissions_Model_Rule::SCOPE_ADMIN, $customer)
                ->process((new Varien_Object())->setCode('dashboard'))->isAccessDenied();

            $as->setUser(Mage::helper('rolespermissions')->getAdminUser($customer));
        }

        $this->_redirectUrl($this->getAdminUrl('adminhtml/sales_order_shipment/view', ['shipment_id' => $this->getRequest()->getParam('shipment_id')]));
    }

    /**
     * @return Mage_Customer_Model_Session
     */
    protected function getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * @return Mage_Admin_Model_Session
     */
    protected function getAdminSession()
    {
        return Mage::getSingleton('admin/session');
    }

    protected function getAdminUrl($route, $params)
    {
        return Mage::getModel('adminhtml/url')->getUrl($route, $params);
    }
}