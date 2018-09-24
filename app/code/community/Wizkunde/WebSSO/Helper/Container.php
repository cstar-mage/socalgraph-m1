<?php

/**
 * Doing it nicely, using the Symfony DIC to get all the logics working
 */
class Wizkunde_WebSSO_Helper_Container
    extends Mage_Core_Helper_Abstract
{
    const XTYPE_WEBSSO_GENERAL_ENABLED = "websso/general/enabled";


    /**
     * @var Symfony2 Dependency Injection Container
     */
    protected $container = null;

    /**
     * @var bool Register the autoloader
     */
    protected static $autoloaderRegistered = false;

    /**
     * Get config data by key
     *
     * @param string $key
     * @return mixed
     */
    public function getConfig($key = '')
    {
        return  Mage::getStoreConfig($key, Mage::app()->getStore()->getId());
    }

    /**
     * Get the Symfony2 Dependency Injection Container
     *
     * @return null|Symfony2
     */
    public function getContainer()
    {
        if($this->container === null) {
            $this->initContainer();
        }

        return $this->container;
    }

    /**
     * Create the Symfony2 Dependency Injection Container
     */
    protected function initContainer()
    {
        $this->addAutoloader();

        $this->container = new Symfony\Component\DependencyInjection\ContainerBuilder();

        include_once(BP . '/lib/GuzzleHttp/functions_include.php');
        include_once(BP . '/lib/GuzzleHttp/Psr7/functions_include.php');
        include_once(BP . '/lib/GuzzleHttp/Promise/functions_include.php');

        /**
         * SAMLBase works with twig templates to do its magic
         */
        $this->container->register('twig_loader', 'Twig_Loader_Filesystem')->addArgument(BP . '/lib/Wizkunde/SAMLBase/Template/Twig');
        $this->container->register('twig', 'Twig_Environment')->addArgument(new Symfony\Component\DependencyInjection\Reference('twig_loader'));

        /**
         * Needed for example for the artifact binding via a backchannel
         */
        $this->container->register('guzzle_http', 'GuzzleHttp\Client');

        $idpData = Mage::helper('websso/data')->getIdpData();

        /**
         * Certificate for signing
         */
        $this->container->register('SigningCertificate', 'Wizkunde\SAMLBase\Certificate')
            ->addMethodCall('setPassphrase', array($idpData->getPassphrase(), false))
            ->addMethodCall('setPublicKey', array($idpData->getCrtData(), false))
            ->addMethodCall('setPrivateKey', array($idpData->getPemData(), false));

        /**
         * Certificate for encryption
         */
        $this->container->register('EncryptionCertificate', 'Wizkunde\SAMLBase\Certificate')
            ->addMethodCall('setPassphrase', array($idpData->getPassphrase(), false))
            ->addMethodCall('setPublicKey', array($idpData->getCrtData(), false))
            ->addMethodCall('setPrivateKey', array($idpData->getPemData(), false));

        /**
         * Base IDP Settings that will be used for the connection
         */
        $this->container->register('samlbase_idp_settings', 'Wizkunde\SAMLBase\Configuration\Settings')
            ->addMethodCall('setValues', array(array(
                'NameID' => $idpData->getNameId(),
                'Issuer' => $idpData->getNameId(),
                'MetadataExpirationTime' => $idpData->getMetadataExpiration(),
                'SPReturnUrl' => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'websso/login/frontend',
                'ForceAuthn' => (int)$idpData->getForceauthn(),
                'IsPassive' => (int)$idpData->getIsPassive(),
                'NameIDFormat' => $idpData->getNameIdFormat(),
                'ComparisonLevel' => 'exact',
                'SsoId' => Mage::getSingleton('core/session')->getSsoSessionId()
            )));

        /**
         * Fetches the active SSO SessionID
         */
        $this->container->register('samlbase_session_id', 'Wizkunde\SAMLBase\Configuration\SessionID');

        /**
         * Possible to retrieve the attributes
         */
        $this->container->register('samlbase_attributes', 'Wizkunde\SAMLBase\Claim\Attributes');

        $this->container->register('samlbase_encryption', 'Wizkunde\SAMLBase\Security\Encryption')
            ->addMethodCall('setCertificate',array(new Symfony\Component\DependencyInjection\Reference('EncryptionCertificate')));

        $this->container->register('samlbase_signature', 'Wizkunde\SAMLBase\Security\Signature')
            ->addMethodCall('setCertificate',array(new Symfony\Component\DependencyInjection\Reference('SigningCertificate')));

        $this->container->register('samlbase_unique_id_generator', 'Wizkunde\SAMLBase\Configuration\UniqueID');
        $this->container->register('samlbase_timestamp_generator', 'Wizkunde\SAMLBase\Configuration\Timestamp');

        /**
         * Setup the Metadata resolve service
         */
        $this->container->register('resolver', 'Wizkunde\SAMLBase\Metadata\ResolveService')
            ->addArgument(new Symfony\Component\DependencyInjection\Reference('guzzle_http'));

        $this->container->register('samlbase_metadata', 'Wizkunde\SAMLBase\Metadata\IDPMetadata');

        /**
         * Todo build in static metadata part
         */

        /**
         * Resolve the metadata
         */
        $metadata = $this->container->get('resolver')->resolve($this->container->get('samlbase_metadata'), $idpData->getMetadataUrl());

        // POST Binding
        $this->container->register('samlbase_binding_post', 'Wizkunde\SAMLBase\Binding\Post')
            ->addMethodCall('setMetadata', array($metadata))
            ->addMethodCall('setTwigService', array(new Symfony\Component\DependencyInjection\Reference('twig')))
            ->addMethodCall('setUniqueIdService', array(new Symfony\Component\DependencyInjection\Reference('samlbase_unique_id_generator')))
            ->addMethodCall('setTimestampService', array(new Symfony\Component\DependencyInjection\Reference('samlbase_timestamp_generator')))
            ->addMethodCall('setSignatureService', array(new Symfony\Component\DependencyInjection\Reference('samlbase_signature')))
            ->addMethodCall('setHttpService', array(new Symfony\Component\DependencyInjection\Reference('guzzle_http')));

        // OR Redirect Binding
        $this->container->register('samlbase_binding_redirect', 'Wizkunde\SAMLBase\Binding\Redirect')
            ->addMethodCall('setMetadata', array($metadata))
            ->addMethodCall('setTwigService', array(new Symfony\Component\DependencyInjection\Reference('twig')))
            ->addMethodCall('setUniqueIdService', array(new Symfony\Component\DependencyInjection\Reference('samlbase_unique_id_generator')))
            ->addMethodCall('setTimestampService', array(new Symfony\Component\DependencyInjection\Reference('samlbase_timestamp_generator')))
            ->addMethodCall('setSignatureService', array(new Symfony\Component\DependencyInjection\Reference('samlbase_signature')))
            ->addMethodCall('setHttpService', array(new Symfony\Component\DependencyInjection\Reference('guzzle_http')));

        // Artifact Resolution over SOAP
        $this->container->register('samlbase_binding_artifact', 'Wizkunde\SAMLBase\Binding\Artifact')
            ->addMethodCall('setMetadata', array($metadata))
            ->addMethodCall('setTwigService', array(new Symfony\Component\DependencyInjection\Reference('twig')))
            ->addMethodCall('setUniqueIdService', array(new Symfony\Component\DependencyInjection\Reference('samlbase_unique_id_generator')))
            ->addMethodCall('setTimestampService', array(new Symfony\Component\DependencyInjection\Reference('samlbase_timestamp_generator')))
            ->addMethodCall('setSignatureService', array(new Symfony\Component\DependencyInjection\Reference('samlbase_signature')))
            ->addMethodCall('setHttpService', array(new Symfony\Component\DependencyInjection\Reference('guzzle_http')));


        $this->container->register('response', 'Wizkunde\SAMLBase\Response\AuthnResponse')
            ->addMethodCall('setSignatureService', array(new Symfony\Component\DependencyInjection\Reference('samlbase_signature')))
            ->addMethodCall('setEncryptionService', array(new Symfony\Component\DependencyInjection\Reference('samlbase_encryption')));

        $this->container->register('logout_response', 'Wizkunde\SAMLBase\Response\LogoutResponse');
    }

    /**
     * Get config data enabled
     *
     * @return mixed
     */
    public function checkEnabled()
    {
        return (bool)Mage::getStoreConfig(self::XTYPE_WEBSSO_GENERAL_ENABLED, Mage::app()->getStore()->getId());
    }

    protected function _getSession()
    {
        return Mage::getSingleton("core/session");
    }

    /**
     * Add a PHP 5.3+ style namespace autoloader from the lib directory
     */
    protected function addAutoloader()
    {
        if (self::$autoloaderRegistered) {
            return;
        }

        spl_autoload_register(array($this, 'autoload'), false, true);
        self::$autoloaderRegistered = true;
    }

    /**
     * The actual autoloader
     *
     * @param $class
     */
    public function autoload($class)
    {
        $classFile = str_replace('\\', '/', $class) . '.php';
        // Only include a namespaced class.  This should leave the regular Magento autoloader alone
        if (strpos($classFile, '/') !== false) {
            include $classFile;
        }
    }
}
