<?php

class Wizkunde_WebSSO_LogoutController
    extends Mage_Core_Controller_Front_Action
{
    /**
     * @var Wizkunde_WebSSO_Model_Claims
     */
    protected $oClaims = null;

    /**
     * @var Mage_Customer_Model_Customer
     */
    protected $oCustomer = null;

    /**
     * Process the actual login
     *
     * @throws Zend_Exception
     */
    public function frontendAction()
    {
        $responseData = Mage::getModel('websso/sso')->getResponseData($_REQUEST);

		$oSession = Mage::getSingleton('customer/session', array('name' => 'frontend'));
        $ssoSessionId = Mage::getSingleton('core/session')->getSsoSessionId();

		if($oSession->isLoggedIn()) {
			$customer = $oSession->getCustomer();
			$oSession->logout();
		}

		if($responseData != null && isset($_REQUEST['SAMLRequest'])) {

			$container = Mage::helper('websso/container')->getContainer();
			$logoutResponse = $container->get('logout_response')->getLogoutData($responseData);

			if($customer->getId() <= 0 && isset($logoutResponse['username'])) {
				$customer = Mage::getModel('customer/customer');
				$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
				$customer->loadByEmail($logoutResponse['username']);
			}

			if($customer->getId() > 0) {
				$this->logoutCustomer($customer);
			}

            Mage::getSingleton('core/session')->setSsoSessionId($ssoSessionId);
            Mage::getModel('websso/sso')->sendLogoutResponse($logoutResponse['sso_id'], $_REQUEST['RelayState']);
		}

		if($responseData == null || isset($_REQUEST['SAMLResponse'])) {
			$this->_redirect('/');
		}
    }

    /**
     * Logout the customer
     */
    protected function logoutCustomer($customer)
    {
        $oSession = Mage::getSingleton('customer/session', array('name' => 'frontend'))->renewSession()->logout();
	
	    $ssoSessionCollection = Mage::getResourceModel('websso/session_collection')->addFilter('customer_id', $customer->getId());
        foreach($ssoSessionCollection as $sessionData) {
            $oSession = Mage::getSingleton('customer/session', array('value' => $sessionData->getSessionId()));
            $oSession->logout();

            $sessionData->delete();
        }
       
        $coreSession = Mage::getSingleton("core/session", array("name"=>"frontend"));
        $coreSession->unsetAll();
    }
}
