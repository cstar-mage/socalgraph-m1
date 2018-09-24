<?php

class Wizkunde_WebSSO_LoginController
    extends Mage_Core_Controller_Front_Action
{
    // Basic information
    const XTYPE_ADDRESS_ENABLED            = 'websso/general/address_enabled';
    const XTYPE_ALLOWED_CREATE             = 'websso/customer/allow_customer_create';
    const XTYPE_UNAUTHORIZED_REDIRECT      = 'websso/general/failed_login_redirect';
    const XTYPE_UNAUTHORIZED_REDIRECT_PAGE = 'websso/general/failed_login_redirect_page';

    const XTYPE_LOGIN_REQUIRED             = 'websso/general/login_required';
    const XTYPE_UPDATE_EXISTING_CUSTOMER   = 'websso/customer/update_existing_customer';

    /**
     * @var Wizkunde_WebSSO_Model_Claims
     */
    protected $oClaims = null;

    /**
     * @var Mage_Customer_Model_Customer
     */
    protected $oCustomer = null;

    public function nossoAction()
    {
        Mage::getSingleton("core/session")->setData('nosso', true);
        $this->_redirectUrl('/');
    }

    public function dossoAction()
    {
        Mage::getSingleton("core/session")->setData('nosso', false);
        $this->_redirectUrl('/');
    }

    public function indexAction()
    {
        $this->_redirectUrl('/');
    }

    public function adminAction()
    {
        Mage::getModel('websso/sso')->login();
    }

    /**
     * Process the actual backend login
     *
     * @throws Zend_Exception
     */
    public function backendAction()
    {
        // Log the incoming claims
        $idpData = Mage::helper('websso/data')->getIdpData();

        if($this->getRequest()->getParam('key')) {
            $claims = Mage::getModel('core/session')->getClaims();

            $session = Mage::getSingleton('websso/backend_session');
            $session->login($claims['username'], Mage::app()->getRequest());

            if($idpData['log_claims'] == true || $idpData['log_debug'] == true) {
                Mage::helper('websso/data')->logEntry('Mapped claims:', $claims, true);
            }
        } else {
            $container = Mage::helper('websso/container')->getContainer();

            $responseData = Mage::getModel('websso/sso')->getResponseData($_REQUEST);
            $sessionId = $container->get('samlbase_session_id')->getIdFromDocument($responseData);

            Mage::getSingleton('core/session')->setSsoSessionId($sessionId);

            $attributes = $container->get('samlbase_attributes')->getAttributes($responseData);

            // Log the incoming attributes
            Mage::helper('websso/data')->logEntry('Incoming Attributes:', $attributes);

            $this->oClaims = Mage::getModel('websso/claims')->mapClaims($attributes);

            Mage::getModel('core/session')->setClaims($this->oClaims->getClaims());

            if($idpData['log_claims'] == true || $idpData['log_debug'] == true) {
                Mage::helper('websso/data')->logEntry('Mapped claims:', $this->oClaims->getClaims(), true);
            }
        }

        /**
         * This is called after login in with the IDP but before logging into magento
         */
        Mage::dispatchEvent(
            'wizkunde_websso_login_process_before',
            array('claims' => $this->oClaims)
        );

        // Load the user
        $this->oUser = Mage::helper('websso/data')->loadUserFromClaims($this->oClaims);

        // If we arent allowed to create a customer and he/she doesnt exist yet, dont login.
        $allowedToCreate = Mage::getStoreConfig(self::XTYPE_ALLOWED_CREATE, Mage::app()->getStore()->getId());

        if($allowedToCreate == false && !$this->oUser->getId()) {
            Mage::getSingleton('core/session')->addError('The login could not be completed');

            if(isset($redirectPage)) {
                $this->_redirectUrl($redirectPage);
            } else {
                $this->_redirectUrl('/');
            }
            return;
        } else if($allowedToCreate == true && !$this->oUser->getId()) {
            /**
             * This is called after loggin in with the IDP but before logging into magento
             */

            $identity = $this->oClaims->getClaim('username');

            if($identity === false || $identity === null) {
                throw new Exception('Provided username was empty');
            }

            try {
                $user = Mage::getModel('websso/backend_user')
                    ->setData($this->oClaims->getClaims());

                $user->addData(array('email' => rand(1000,9999) . $this->oClaims->getClaim('username'), 'password' => md5(uniqid()), 'is_active' => 1));
                $user->save();
            } catch (Exception $e) {
                echo $e->getMessage();
                exit;
            }

            $role = ($this->oClaims->getClaim('role') > 0) ? $this->oClaims->getClaim('role') : 1;

            //Assign Role Id
            try {
                $user->setRoleIds(array($role))  //Administrator role id is 1 ,Here you can assign other roles ids
                ->setRoleUserId($user->getUserId())
                    ->saveRelations();

            } catch (Exception $e) {
                echo $e->getMessage();
                exit;
            }

            // Load the customer
            $this->oUser = Mage::helper('websso/data')->loadUserFromClaims($this->oClaims);
        }

        if ($this->oUser->getId()) {
            $session = Mage::getSingleton('websso/backend_session');
            $session->login($this->oUser->getUsername(), 'notusedpassword', Mage::app()->getRequest());
            $session->setUser($this->oUser);
        } else {
            throw new Zend_Exception('Something went wrong, no customer is matched to login to');
        }
    }

    /**
     * Process the actual login
     *
     * @throws Zend_Exception
     */
    public function frontendAction()
    {
        $container = Mage::helper('websso/container')->getContainer();

        // Log the incoming claims
        $idpData = Mage::helper('websso/data')->getIdpData();

        $responseData = Mage::getModel('websso/sso')->getResponseData($_REQUEST);

        $ssoSessionId = $container->get('samlbase_session_id')->getSessionIdFromDocument($responseData);

        if($ssoSessionId == null) {
            $ssoSessionId = $container->get('samlbase_session_id')->getIdFromDocument($responseData);
        }

        Mage::getSingleton('core/session')->setSsoSessionId($ssoSessionId);

        $attributes = $container->get('samlbase_attributes')->getAttributes($responseData);

        // Log the incoming attributes
        Mage::helper('websso/data')->logEntry('Incoming Attributes:', $attributes);

        $this->oClaims = Mage::getModel('websso/claims')->mapClaims($attributes);

        if($idpData['log_claims'] == true || $idpData['log_debug'] == true) {
            Mage::helper('websso/data')->logEntry('Mapped claims:', $this->oClaims->getClaims(), true);
        }

        /**
         * This is called after login in with the IDP but before logging into magento
         */
        Mage::dispatchEvent(
            'wizkunde_websso_login_process_before',
            array('claims' => $this->oClaims)
        );

        // Load the customer
        $this->oCustomer = Mage::helper('websso/data')->loadCustomerFromClaims($this->oClaims);

        // If we arent allowed to create a customer and he/she doesnt exist yet, dont login.
        $allowedToCreate = Mage::getStoreConfig(self::XTYPE_ALLOWED_CREATE, Mage::app()->getStore()->getId());

        if($allowedToCreate == false && !$this->oCustomer->getId()) {
            Mage::getSingleton('core/session')->addError('The login could not be completed');

            $unauthorizedRedirect = Mage::getStoreConfig(self::XTYPE_UNAUTHORIZED_REDIRECT, Mage::app()->getStore()->getId());
            if($unauthorizedRedirect === true) {
                $redirectPage = Mage::getStoreConfig(self::XTYPE_UNAUTHORIZED_REDIRECT_PAGE, Mage::app()->getStore()->getId());
            }

            if(isset($redirectPage)) {
                $this->_redirectUrl($redirectPage);
            } else {
                $this->_redirectUrl('/');
            }
            return;
        } else if($allowedToCreate == true && !$this->oCustomer->getId()) {
            /**
             * This is called after loggin in with the IDP but before logging into magento
             */
            Mage::dispatchEvent(
                'wizkunde_websso_user_create',
                array(
                    'claims'    => $this->oClaims
                )
            );

            // Load the customer
            $this->oCustomer = Mage::helper('websso/data')->loadCustomerFromClaims($this->oClaims);
        }

        /**
         * This is called after loggin in with the IDP but before logging into magento
         */
        Mage::dispatchEvent(
            'wizkunde_websso_user_update',
            array(
                'claims'    => $this->oClaims,
                'customer'  => $this->oCustomer
            )
        );

        /**
         * This is called after loggin in with the IDP but before logging into magento
         */
        Mage::dispatchEvent(
            'wizkunde_websso_user_login_process_before',
            array(
                'claims'    => $this->oClaims,
                'customer'  => $this->oCustomer
            )
        );

        $oSession = Mage::getSingleton('customer/session', array('name' => 'frontend'));
        if ($this->oCustomer->getId()) {
            $oSession->renewSession()->loginById($this->oCustomer->getId());
	    //die("login time");
            // Customer, Session, SSO Session ID
            $ssoSession = Mage::getModel('websso/session');
            $ssoSession->addData(
                array(
                    'customer_id' => $this->oCustomer->getId(),
                    'session_id' => $oSession->getSessionId(),
                    'sso_session' => $ssoSessionId
                )
            );
            $ssoSession->save();
        } else {
            throw new Zend_Exception('Something went wrong, no customer is matched to login to');
        }

        /**
         * This is called after loggin in with the IDP but before logging into magento
         */
        Mage::dispatchEvent(
            'wizkunde_websso_login_process_after',
            array(
                'claims' => $this->oClaims,
                'customer'  => $this->oCustomer
            )
        );

        $this->_redirectUrl(Mage::helper('websso')->getBeforeLoginUrl());
    }
}
