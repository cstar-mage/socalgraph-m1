<?php

/**
 * Event Model that will enforce login if its enabled and the customer is not logged in
 *
 * Class Wizkunde_WebSSO_Model_Requirelogin
 */
class Wizkunde_WebSSO_Model_Frontend
{
    const _LOGIN_MANDATORY    = "websso/general/login_required";
    const _LOGIN_REGULAR    = "websso/general/login_regular";
    const _LOGIN_ROUTE    = "websso/general/login_route";
    const _NOSSO_ROUTE = 'websso/login/nosso';

    /**
     * @param $observer
     * @return $this
     */
    public function _startSSO(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('websso/data')->checkEnabled()) {
            return $this;
        }

        // Dont redirect if we are already returning to a login response
        if (strpos(Mage::helper('core/url')->getCurrentUrl(), 'websso/login/frontend') != false) {
            return $this;
        }


        // Allow SSO to be surpassed
        if (strpos(Mage::helper('core/url')->getCurrentUrl(), 'websso/login/nosso') != false) {
            return $this;
        }

        // Allow SSO override to be disabled again
        if (strpos(Mage::helper('core/url')->getCurrentUrl(), 'websso/login/dosso') != false) {
            return $this;
        }

        // Ignore if we are in the admin section
        if (strpos(Mage::helper('core/url')->getCurrentUrl(), Mage::getUrl('adminhtml')) !== false) {
            return $this;
        }

        $loginRoute = Mage::getStoreConfig(self::_LOGIN_ROUTE, Mage::app()->getStore()->getId());

        if($this->mustRedirectToLoginPage() == true) {
            Mage::app()->getResponse()->setRedirect(Mage::getUrl('/', array("_secure" => $_SERVER["HTTPS"] === "on")) . $loginRoute);
            return $this;
        }

        if (!Mage::getModel('customer/session')->isLoggedIn() && strpos(Mage::helper('core/url')->getCurrentUrl(), $loginRoute) != false) {
            Mage::getModel('websso/sso')->login($this);
        }
    }

    /**
     * Check if we need to redirect to the login page
     *
     * @return bool
     */
    protected function mustRedirectToLoginPage()
    {
        $loginRoute = Mage::getStoreConfig(self::_LOGIN_ROUTE, Mage::app()->getStore()->getId());

        if(Mage::getSingleton("core/session")->getData('nosso') === true) {
            return false;
        }

        if (Mage::getStoreConfig(self::_LOGIN_MANDATORY, Mage::app()->getStore()->getId()) == true &&
            !Mage::getModel('customer/session')->isLoggedIn() &&
            strpos(Mage::helper('core/url')->getCurrentUrl(), '/websso/artifact/soap') === false &&
            strpos(Mage::helper('core/url')->getCurrentUrl(), 'logout') === false &&
            strpos(Mage::helper('core/url')->getCurrentUrl(), $loginRoute) == false) {
                return true;
        }

        // If we are at the customer login page and regular login is not allowed
        if (strpos(Mage::helper('core/url')->getCurrentUrl(), 'customer/account/login') != false &&
            !Mage::getModel('customer/session')->isLoggedIn() &&
            Mage::getStoreConfig(self::_LOGIN_REGULAR, Mage::app()->getStore()->getId()) == false
        ) {
                return true;
        }

        return false;
    }
}
