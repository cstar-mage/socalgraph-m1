<?php

use \RobRichards\XMLSecLibs\XMLSecurityDSig;

class Wizkunde_WebSSO_MetadataController
    extends Mage_Core_Controller_Front_Action
{
    /**
     * Show the metadata
     */
    public function indexAction()
    {
        $container = Mage::helper('websso/container')->getContainer();

        $idpData = Mage::helper('websso/data')->getIdpData();

        $requestTemplate = $container->get('twig')->render('Metadata.xml.twig',
            array(
                'BaseURL'                   => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB),
                'ACSURL'                   => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'websso/login/frontend',
                'SLOURL'                   => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'websso/logout/frontend',
                'EntityID'                  => $idpData->getNameId(),
                'ServiceProviderPublicKey'  => Mage::helper('websso/data')->getCrtString(),
                'OrganizationName'          => Mage::app()->getWebsite()->getName(),
                'OrganizationDisplayName'   => Mage::app()->getWebsite()->getName(),
                'OrganizationURL'           => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB),
                'ContactPersonSurName'      => Mage::getStoreConfig('trans_email/ident_general/name'),
                'ContactPersonEmailAddress' => Mage::getStoreConfig('trans_email/ident_general/email')
            )
        );

        $document = new \DOMDocument();
        $document->loadXML($requestTemplate);

        $container->get('samlbase_signature')->signMetadata($document);
        
        header('Content-Type: application/xml');
        echo $document->saveXML();
        exit;
    }

    /**
     * Show the metadata
     */
    public function backendAction()
    {
        $container = Mage::helper('websso/container')->getContainer();

        $idpData = Mage::helper('websso/data')->getIdpData();

        $requestTemplate = $container->get('twig')->render('Metadata.xml.twig',
            array(
                'BaseURL'                   => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB),
                'ACSURL'                   => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'websso/login/backend',
                'SLOURL'                   => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'websso/logout/backend',
                'EntityID'                  => $idpData->getNameId(),
                'ServiceProviderPublicKey'  => Mage::helper('websso/data')->getCrtString(),
                'OrganizationName'          => Mage::app()->getWebsite()->getName(),
                'OrganizationDisplayName'   => Mage::app()->getWebsite()->getName(),
                'OrganizationURL'           => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB),
                'ContactPersonSurName'      => Mage::getStoreConfig('trans_email/ident_general/name'),
                'ContactPersonEmailAddress' => Mage::getStoreConfig('trans_email/ident_general/email')
            )
        );

        $document = new \DOMDocument();
        $document->loadXML($requestTemplate);

        $container->get('samlbase_signature')->signMetadata($document);

        header('Content-Type: application/xml');
        echo $document->saveXML();
        exit;
    }
}

