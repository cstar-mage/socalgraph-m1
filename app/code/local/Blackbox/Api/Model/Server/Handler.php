<?php

class Blackbox_Api_Model_Server_Handler extends Mage_Api_Model_Server_Handler_Abstract
{
    /**
     * @return Blackbox_Api_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('api/session', array('rest' => true));
    }

    public function __construct()
    {
        set_error_handler(array($this, 'handlePhpError'), E_ALL);
        Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_FRONTEND, Mage_Core_Model_App_Area::PART_EVENTS);
    }

    /**
     * Start webservice session
     *
     * @param string $sessionId
     * @return Mage_Api_Model_Server_Handler_Abstract
     */
    protected function _startSession($sessionId=null)
    {
        $this->_getSession()->setSessionId($sessionId);
        $this->_getSession()->init('restapi', 'restapi');
        return $this;
    }

    public function login(Zend_Controller_Request_Http $request)
    {
        $this->_getSession()->loginRest($request, 'restapi', 'restapi');
    }

    /**
     * Call resource functionality
     *
     * @param string $sessionId
     * @param string $apiPath
     * @param array  $args
     * @return mixed
     */
    public function call($sessionId, $apiPath, $args = array())
    {
        $this->_startSession($sessionId);

        $path = explode('/', trim($apiPath, '/'));
        $count = count($path);

        if ($count < 2) {
            throw new Blackbox_Api_Exception('Bad request', Blackbox_Api_Model_Server_Adapter_Rest::HTTP_BAD_REQUEST);
        }

        if ($path[2] != 'oauth') {
            if (!$this->_getSession()->isLoggedIn($sessionId)) {
                return $this->_fault('session_expired');
            }
        }

        switch ($count) {
            case 2:
                $result = $this->_processResourcesDefenition();
                break;
            case 3:
                $resource = $path[2];

                $result = $this->_processResourceDefinition($resource);
                break;
            case 4:
                $resource = $path[2];
                $method = $path[3];

                try {
                    $result = $this->_processResourceCall($resource, $method, $args);
                } catch (Mage_Api_Exception $e) {
                    $this->_fault($e->getMessage(), $resource, $e->getCustomMessage());
                }
                break;
            default:
                throw new Blackbox_Api_Exception('Bad request', Blackbox_Api_Model_Server_Adapter_Rest::HTTP_BAD_REQUEST);
        }

        return $result;
    }

    protected function _processResourceCall($resource, $method, $params)
    {
        /* @var Mage_Api_Model_Config $config */
        $config   = Mage::getSingleton('api/config');

        $node = $config->getNode('resources/' . $resource);

        $model = Mage::getSingleton($node->model);
        if (!$model) {
            throw new Blackbox_Api_Exception('Invalid resource', Blackbox_Api_Model_Server_Adapter_Rest::HTTP_BAD_REQUEST);
        }

        $methodNode = $node->methods->$method;
        if (!$methodNode) {
            throw new Blackbox_Api_Exception('Invalid method', Blackbox_Api_Model_Server_Adapter_Rest::HTTP_BAD_REQUEST);
        }

        if ($methodNode->acl && !$this->isAllowed((string)$methodNode->acl)) {
            throw new Blackbox_Api_Exception('Access Denied', Blackbox_Api_Model_Server_Adapter_Rest::HTTP_METHOD_NOT_ALLOWED);
        }

        $methodName = $this->_getRealMethodName($methodNode);

        if (!method_exists($model, $methodName)) {
            throw new Blackbox_Api_Exception('Internal error: method doesn\'t exist', Blackbox_Api_Model_Server_Adapter_Rest::HTTP_INTERNAL_ERROR);
        }

        $reflection = new ReflectionMethod($model, $methodName);

        $funcParams = array();

        foreach($reflection->getParameters() as $parameter) {
            $name = $this->_uncamelize($parameter->name);
            if (!isset($params[$name])) {
                if ($parameter->isOptional()) {
                    try {
                        $funcParams[] = $parameter->getDefaultValue();
                        continue;
                    } catch (Exception $e) {}
                }
                throw new Blackbox_Api_Exception('Missing parameter ' . $name, Blackbox_Api_Model_Server_Adapter_Rest::HTTP_BAD_REQUEST);
            }
            $funcParams[] = $params[$name];
        }

        $response = call_user_func_array(array($model, $methodName), $funcParams);
        $result = array('response' => $response);

        return $result;
    }

    protected function _processResourcesDefenition()
    {
        /* @var Mage_Api_Model_Config $config */
        $config   = Mage::getSingleton('api/config');

        $resources = $config->getNode('resources');

        $result = array();
        foreach ($resources->children() as $resource) {
            if ($resource->acl && !$this->isAllowed((string)$resource->acl)) {
                continue;
            }
            $result[$resource->getName()] = $this->_getResourceDefinition($resource);
        }

        return array('resources' => $result);
    }

    protected function _processResourceDefinition($resource)
    {
        /* @var Mage_Api_Model_Config $config */
        $config   = Mage::getSingleton('api/config');

        $node = $config->getNode('resources/' . $resource);

        if ($node->acl && !$this->isAllowed((string)$node->acl)) {
            throw new Blackbox_Api_Exception('Access Denied', Blackbox_Api_Model_Server_Adapter_Rest::HTTP_METHOD_NOT_ALLOWED);
        }

        $definition = $this->_getResourceDefinition($node);

        return array(
            $node->getName() => $definition
        );
    }

    protected function _getResourceDefinition($resourceNode)
    {
        $model = Mage::getSingleton($resourceNode->model);
        if (!$model) {
            throw new Blackbox_Api_Exception('Invalid resource', Blackbox_Api_Model_Server_Adapter_Rest::HTTP_BAD_REQUEST);
        }

        $result = array(
            'title' => (string)$resourceNode->title
        );
        $methods = array();
        foreach ($resourceNode->methods->children() as $methodNode) {
            if ($methodNode->acl && !$this->isAllowed((string)$methodNode->acl)) {
                continue;
            }

            $methodName = $this->_getRealMethodName($methodNode);

            if (!method_exists($model, $methodName)) {
                continue;
            }

            $reflection = new ReflectionMethod($model, $methodName);
            $params = array();

            foreach ($reflection->getParameters() as $parameter) {
                $param = array(
                    'name' => $this->_uncamelize($parameter->name)
                );
                try {
                    $param['default_value'] = $parameter->getDefaultValue();
                } catch (Exception $e) {
                    if ($parameter->isOptional()) {
                        $param['optional'] = true;
                    }
                }
                $params[] = $param;
            }

            $methods[$methodNode->getName()] = array(
                'title' => (string)$methodNode->title,
                'parameters' => $params,
            );
        }

        $result['methods'] = $methods;

        return $result;
    }

    protected function _getRealMethodName($methodNode)
    {
        if ($methodNode->method) {
            return (string)$methodNode->method;
        } else {
            return $methodNode->getName();
        }
    }

    protected function _uncamelize($str)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $str));
    }

    /**
     * Check current user permission on resource
     *
     *
     * @param   string $resource
     * @return  bool
     */
    public function isAllowed($resource)
    {
        return $this->_getSession()->isAllowed($resource);
    }
}