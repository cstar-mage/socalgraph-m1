<?php

/**
 * Be able to return to the checkout process if OneStepCheckout is installed
 */
class Wizkunde_WebSSO_Helper_Data
    extends Mage_Core_Helper_Abstract
{
    const SESSION_BEFORE_LOGIN_VAR      = "url_before_login";

    const XTYPE_WEBSSO_GENERAL_ENABLED  = "websso/general/frontend_enabled";
    const XTYPE_WEBSSO_GENERAL_BACKEND_ENABLED  = "websso/general/backend_enabled";
    const XTYPE_WEBSSO_IDENTITY_FIELD   = 'websso/advanced/identity_field';

    const XTYPE_WEBSSO_METADATA_IDP = "websso/metadata/idp";

    /**
     * @var Mage_Customer_Model_Customer
     */
    protected $customer = null;

    /**
     * Get before login url
     *
     * @return string
     */
    public function getBeforeLoginUrl()
    {
        if (strpos(Mage::getSingleton("core/session")->getData(self::SESSION_BEFORE_LOGIN_VAR), "onestepcheckout")) {
            return Mage::getUrl('onestepcheckout');
        }

        if(Mage::getSingleton("core/session")->getData(self::SESSION_BEFORE_LOGIN_VAR)) {
            return Mage::getSingleton("core/session")->getData(self::SESSION_BEFORE_LOGIN_VAR);
        }

        return Mage::getUrl('/');
    }

    /**
     * Set before login url
     */
    public function setBeforeLoginUrl($url = null)
    {
        Mage::getSingleton("core/session")->setData(
            self::SESSION_BEFORE_LOGIN_VAR,
            $url ?: Mage::getSingleton("core/session")->getData('last_url')
        );
    }

    public function loadUserFromClaims($claims)
    {
        $identity = $claims->getClaim('username');

        if($identity == '') {
            throw new Zend_Exception('Login could not be completed because the login username was empty');
        }

        $this->user = Mage::getModel('admin/user')->loadByUsername($claims->getClaim('username'));

        return $this->user;
    }

    public function loadCustomerFromClaims($claims)
    {
        /**
         * This is called after login in with the IDP but before logging into magento
         */
        Mage::dispatchEvent(
            'wizkunde_websso_load_customer_before',
            array('claims' => $claims)
        );

        $identity = $claims->getClaim('email');

        if($identity == '') {
            throw new Zend_Exception('Login could not be completed because the mapping for the identity field "email" was not made');
        }

        $this->customer = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToFilter('email', $identity)
            ->getFirstItem();

        /**
         * This is called after loggin in with the IDP but before logging into magento
         */
        Mage::dispatchEvent(
            'wizkunde_websso_load_customer_after',
            array(
                'claims' => $claims,
                'customer'  => $this->customer
            )
        );

        return $this->customer;
    }

    /**
     * Get the metadata that is set in Magento configuration
     *
     * @return mixed
     * @throws Zend_Exception
     */
    public function getIdpData()
    {
        if(isset($_REQUEST['idp'])) {
            $idpIdentifier = strip_tags(urlencode($_REQUEST['idp']));
        } else{
            $idpIdentifier = Mage::getStoreConfig(self::XTYPE_WEBSSO_METADATA_IDP, Mage::app()->getStore()->getId());
        }

        if(!isset($idpIdentifier) || $idpIdentifier == '') {
            throw new Zend_Exception('Unable to load identity provider data, none set in the system configuration');
        }

        return Mage::getModel('websso/idp')->getCollection()->addFieldToFilter('identifier', $idpIdentifier)->getFirstItem();
    }

    /**
     * Return the public key data
     *
     * @return mixed
     * @throws Zend_Exception
     */
    public function getCrtString()
    {
        return str_replace(
            array(
                '-----BEGIN CERTIFICATE-----',
                '-----END CERTIFICATE-----',
                "\n",
                "\r"
            ),
            '',
            $this->getIdpData()->getCrtData()
        );
    }

    /**
     * Get config data enabled
     *
     * @return mixed
     */
    public function checkEnabled()
    {
        //return false;
        if(Mage::getSingleton("core/session")->getData('nosso') == true) {
            return false;
        }

        return (bool)Mage::getStoreConfig(self::XTYPE_WEBSSO_GENERAL_ENABLED, Mage::app()->getStore()->getId());
    }

    /**
     * Get config data enabled
     *
     * @return mixed
     */
    public function checkAdminEnabled()
    {
        //return false;
        if(Mage::getSingleton("core/session")->getData('nosso') == true) {
            return false;
        }

        return (bool)Mage::getStoreConfig(self::XTYPE_WEBSSO_GENERAL_BACKEND_ENABLED, Mage::app()->getStore()->getId());
    }

    public function logEntry($name, $data, $forceLog = false)
    {
        $idpData = $this->getIdpData();

        if($idpData['log_debug'] == true || $forceLog == true || true) {
            Mage::log($name, null, 'wizkunde_websso.log', true);
            Mage::log($data, null, 'wizkunde_websso.log', true);
        }
    }
}
