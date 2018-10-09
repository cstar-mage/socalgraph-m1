<?php

class Blackbox_Api_Model_Auth_Oauth
{
    protected $_user = null;
    protected $_customer = null;

    public function login(Zend_Controller_Request_Http $request)
    {
        /** @var $oauthServer Mage_Oauth_Model_Server */
        $oauthServer   = Mage::getModel('oauth/server', $request);

        try {
            $token    = $oauthServer->checkAccessRequest();
            $this->_user = Mage::getModel('blackbox_api/user')->loadByToken($token);
            if ($token->getCustomerId()) {
                $this->_customer = Mage::getModel('customer/customer')->load($token->getCustomerId());
            }
        } catch (Exception $e) {
            throw new Mage_Api2_Exception($oauthServer->reportProblem($e), Blackbox_Api_Model_Server_Adapter_Rest::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @return null|Blackbox_Api_Model_User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @return null|Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return $this->_customer;
    }
}