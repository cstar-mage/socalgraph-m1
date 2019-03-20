<?php

/**
 * Class Wizkunde_WebSSO_Model_Sso
 */
class Wizkunde_WebSSO_Model_Sso
{
    const BINDING_REDIRECT = 0;
    const BINDING_POST = 1;

    const XTYPE_SSO_BINDING        = 'websso/general/sso_binding';
    const XTYPE_SLO_BINDING        = 'websso/general/slo_binding';

    /**
     * Login with the Identity Provider
     */
    public function login($observer)
    {
        if (!Mage::helper('websso/data')->checkEnabled()) {
            return $this;
        }

        $this->request('AuthnRequest');
    }

    /**
     * Logout from the Identity Provider
     */
    public function logout($observer)
    {
        if (!Mage::helper('websso/data')->checkEnabled()) {
            return $this;
        }

        $this->request('LogoutRequest', 'slo');
    }

    /**
     * Logout from the Identity Provider
     */
    public function sendLogoutResponse($ssoId, $relayState = '')
    {
        if (!Mage::helper('websso/data')->checkEnabled()) {
            return $this;
        }

        $this->request('LogoutResponse', 'slo', $ssoId, $relayState);
    }

    /**
     * Do the SSO/SLO Request via the appropriate binding
     *
     * @param string $type
     * @throws Zend_Exception
     */
    protected function request($type = 'AuthnRequest', $requestType = 'sso', $ssoId = '', $relayState = '')
    {
        $container = Mage::helper('websso/container')->getContainer();

        $idpData = Mage::helper('websso/data')->getIdpData();

        $bindingType = ($requestType == 'slo') ? $idpData->getSloBinding() : $idpData->getSsoBinding();
        if ($bindingType == 2) {
            return;
        }

        switch($bindingType) {
            case self::BINDING_REDIRECT:
                $binding = $container->get('samlbase_binding_redirect');
                break;
            case self::BINDING_POST:
                $binding = $container->get('samlbase_binding_post');
                break;
            default:
                throw new Zend_Exception('Unrecognized binding for SSO request');
                break;
        }

	    $settings = $container->get('samlbase_idp_settings');

        if($requestType == 'slo' && $ssoId !=  '') {
            $settings->setValue('SsoId', $ssoId);
        }

        if(Mage::getSingleton('core/session')->getSsoSessionId() != null) {
            $settings->setValue('SessionID', Mage::getSingleton('core/session')->getSsoSessionId());
        }

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $settings->setValue('email', $customer->getEmail());
        }

        $binding->setSettings($settings);
        $binding->request($type, $relayState);
    }

    /**
     * Get the Responsedata from the request object
     *
     * @param $requestData
     * @return mixed
     */
    public function getResponseData($requestData)
    {
        $container = Mage::helper('websso/container')->getContainer();

        if(isset($requestData['SAMLart'])) {
            return $this->resolveArtifact($requestData['SAMLart']);
        } else if(isset($requestData['SAMLResponse'])) {
            return $container->get('response')->decode($_REQUEST['SAMLResponse']);
        } else if(isset($requestData['SAMLRequest'])) {
            return $container->get('response')->decode($_REQUEST['SAMLRequest']);
        } else {
            throw new Zend_Exception('Unable to parse response, no valid SAML response');
        }
    }

    /**
     * Resolve the SAML2 Artifact if it was set in the response
     *
     * @param $artifact
     * @return mixed
     */
    protected function resolveArtifact($artifact)
    {
        $container = Mage::helper('websso/container')->getContainer();

        return $container->get('samlbase_binding_artifact')
            ->setSettings($container->get('samlbase_idp_settings'))
            ->resolveArtifact($artifact);
    }
}
