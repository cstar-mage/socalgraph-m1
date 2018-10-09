<?php

class Blackbox_Api_Model_Server_Adapter_Rest extends Varien_Object
    implements Mage_Api_Model_Server_Adapter_Interface
{
    protected $_user = null;
    protected $_acl = null;
    protected $_authUser = null;
    protected $_handler = null;

    /**#@+
     * HTTP Response Codes
     */
    const HTTP_OK                 = 200;
    const HTTP_CREATED            = 201;
    const HTTP_MULTI_STATUS       = 207;
    const HTTP_BAD_REQUEST        = 400;
    const HTTP_UNAUTHORIZED       = 401;
    const HTTP_FORBIDDEN          = 403;
    const HTTP_NOT_FOUND          = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE     = 406;
    const HTTP_INTERNAL_ERROR     = 500;

    public function _construct()
    {
        parent::_construct();
        $this->setResponseCode(self::HTTP_OK);
    }

    public function run()
    {
        try {
            $request = $this->getController()->getRequest();

            $handler = $this->_getHandlerInstance();

            $requetPath = explode('/', trim($request->getRequestString(), '/'));
            if ($requetPath[2] != 'oauth') {
                $handler->login($request);
            }

            Mage::setIsDeveloperMode(true);

            $params = $this->_getRequestParams();
            Mage::log('REQUEST ' . $request->getMethod() . $request->getPathInfo() . PHP_EOL .
                'Content Type ' . $_SERVER['CONTENT_TYPE'] . PHP_EOL .
                'params ' . print_r($params, true) .
                '$_REQUEST ' . print_r($_REQUEST, true) .
                ($request->getMethod() == 'POST' ? 'RAW BODY ' . $request->getRawBody() : '') . PHP_EOL
                , null, 'api.log');

            $result = $handler->call($this->_getSession()->getSessionId(), $request->getPathInfo(), $params);
        } catch (Blackbox_Api_Exception $e) {
            $this->setResponseCode($e->getCode());
            $result = array('error' => array(
                'message'   => $e->getMessage(),
            ));
        } catch (Mage_Api_Exception $e) {
            $this->setResponseCode(self::HTTP_BAD_REQUEST);
            $result = array('error' => array(
                'message'   => $e->getCustomMessage(),
                'fault_code' => $e->getMessage(),
                'code'      => $e->getCode(),
            ));
        } catch (Exception $e) {
            $this->setResponseCode(self::HTTP_BAD_REQUEST);
            $result = array('error' => array(
                'message'   => $e->getMessage(),
                'code'      => $e->getCode(),
            ));
        }

        $response = json_encode($result, isset($params) && $params['json_pretty_print'] ? JSON_PRETTY_PRINT : null);

        $this->getController()->getResponse()
            ->clearHeaders()
            ->setHttpResponseCode($this->getResponseCode())
            ->setHeader('Content-Type','application/json')
            ->setBody(
                $response
            );

        Mage::log('RESPONSE ' . Mage::helper('core/string')->truncate($response, 300) . "\n\n\n", null, 'api.log');
    }

    /**
     * Authenticate user
     *
     * @throws Exception
     * @param Zend_Controller_Request_Http $request
     * @return $this
     */
    protected function _authenticate(Zend_Controller_Request_Http $request)
    {
        $this->_getSession()->login($request);
        return $this;
    }

    /**
     * @return Blackbox_Api_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('api/session', array('rest' => true));
    }

    protected function _getRequestParams()
    {
        $request = $this->getController()->getRequest();

        if ($request->isPost()) {
            if (strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false
                || strpos($_SERVER['CONTENT_TYPE'], 'text/plain') !== false) {
                $requestParams = json_decode($request->getRawBody(), true);
            } else {
                $requestParams = $request->getPost();
            }
        } else {
            $requestParams = $request->getParams();
        }

        return $requestParams;
    }

    public function getController()
    {
        $controller = $this->getData('controller');

        if (null === $controller) {
            $controller = new Varien_Object(
                array('request' => Mage::app()->getRequest(), 'response' => Mage::app()->getResponse())
            );

            $this->setData('controller', $controller);
        }
        return $controller;
    }

    public function fault($code, $message)
    {
        switch ($code) {
            case 0:
                $code = self::HTTP_BAD_REQUEST;
                break;
            case 1:
                $code = self::HTTP_INTERNAL_ERROR;
                break;
            case 2:
                $code = self::HTTP_FORBIDDEN;
                break;
            case 3:
                $code = self::HTTP_NOT_FOUND;
                break;
            case 4:
                $code = self::HTTP_BAD_REQUEST;
                break;
            case 5:
                $code = self::HTTP_UNAUTHORIZED;
                break;
            case 6:
                $code = self::HTTP_BAD_REQUEST;
                break;
            default:
                throw new Mage_Exception($message, $code);
        }
        throw new Blackbox_Api_Exception($message, $code);
    }

    public function getHandler()
    {
        return $this->getData('handler');
    }

    public function setController(Mage_Api_Controller_Action $controller)
    {
        $this->setData('controller', $controller);
        return $this;
    }

    public function setHandler($handler)
    {
        $this->setData('handler', $handler);
        return $this;
    }

    /**
     * @return Blackbox_Api_Model_Server_Handler
     */
    protected function _getHandlerInstance()
    {
        if (!$this->_handler) {
            $handlerClassName = $this->getHandler();

            if ($handlerClassName) {
                $this->_handler = new $handlerClassName();
            }
        }
        return $this->_handler;
    }
}