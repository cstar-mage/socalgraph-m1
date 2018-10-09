<?php

class Blackbox_Api_Model_Oauth_Server extends Mage_Oauth_Model_Server
{
    /**
     * @param string $consumerKey
     * @return Mage_Oauth_Model_Token
     */
    public function generateAccessToken($consumerKey)
    {
        $this->_protocolParams['oauth_consumer_key'] = $consumerKey;

        $this->_initConsumer();

        $this->_requestType = self::REQUEST_INITIATE;
        $this->_token = Mage::getModel('oauth/token');
        $this->_protocolParams['oauth_callback'] = 'http://yokohamaprint.com';
        $this->_saveToken();
        $this->_requestType = self::REQUEST_TOKEN;
        $this->_saveToken();

        return $this->_token;
    }
}