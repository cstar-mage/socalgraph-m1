<?php

class Blackbox_Epace_Helper_Api extends Mage_Core_Helper_Abstract
{
    const JOB_STATUS_OPEN = 'O';
    const JOB_STATUS_CLOSED = 'C';

    /** @var Blackbox_Soap_Model_Api */
    protected $api;
    protected $namespace = 'soap';
    protected $auth;
    protected $username, $password, $baseUrl, $company;
    protected $event = null;

    public function __construct()
    {
        $this->api = Mage::getModel('blackbox_soap/api', array(
            'baseUrl' => $this->baseUrl = Mage::getStoreConfig('epace/main_settings/base_url'),
            'actionBaseUrl' => '',
            'namespace' => $this->namespace
        ));

        $this->company = Mage::getStoreConfig('epace/main_settings/company');

        $this->setAuthInfo(Mage::getStoreConfig('epace/main_settings/username'), Mage::getStoreConfig('epace/main_settings/password'));
    }

    public function setAuthInfo($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
        $this->auth = base64_encode($username . ':' . $password);
    }

    public function setCompany($company)
    {
        $this->company = $company;
    }

    public function setEvent(Blackbox_Epace_Model_Event $event)
    {
        if ($event == null) {
            $this->api->setLogCallback(null);
        } else {
            $this->event = $event;
            $this->api->setLogCallback(array($this, 'logCallback'));
        }
    }

    /**
     * @return Blackbox_Epace_Model_Event|null
     */
    public function getEvent()
    {
        return $this->event;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getHost()
    {
        return $this->baseUrl;
    }

    public function readCostCenter($id)
    {
        $params = array(
            'readCostCenter' => array(
                'xmlns' => 'urn://pace2020.com/epace/sdk/ReadObject',
                'costCenter' => array(
                    'id' => array(
                        'xmlns' => 'http://pace2020.com/epace/object',
                        $id
                    ),
                    'description' => array(
                        '_attributes' => array(
                            'xsi:nil' => array(
                                'value' => 'true',
                                'namespace' => 'http://www.w3.org/2001/XMLSchema-instance'
                            )
                        ),
                        'xmlns' => 'http://pace2020.com/epace/object'
                    ),
                    'department' => array(
                        '_attributes' => array(
                            'xsi:nil' => array(
                                'value' => 'true',
                                'namespace' => 'http://www.w3.org/2001/XMLSchema-instance'
                            )
                        ),
                        'xmlns' => 'http://pace2020.com/epace/object'
                    ),
                    'hoursAvailable' => array(
                        '_attributes' => array(
                            'xsi:nil' => array(
                                'value' => 'true',
                                'namespace' => 'http://www.w3.org/2001/XMLSchema-instance'
                            )
                        ),
                        'xmlns' => 'http://pace2020.com/epace/object'
                    ),
                    'printFlowClass' => array(
                        '_attributes' => array(
                            'xsi:nil' => array(
                                'value' => 'true',
                                'namespace' => 'http://www.w3.org/2001/XMLSchema-instance'
                            )
                        ),
                        'xmlns' => 'http://pace2020.com/epace/object'
                    ),
                    'jdfSubmitMethod' => array(
                        '_attributes' => array(
                            'xsi:nil' => array(
                                'value' => 'true',
                                'namespace' => 'http://www.w3.org/2001/XMLSchema-instance'
                            )
                        ),
                        'xmlns' => 'http://pace2020.com/epace/object'
                    ),
                    'webCam' => array(
                        '_attributes' => array(
                            'xsi:nil' => array(
                                'value' => 'true',
                                'namespace' => 'http://www.w3.org/2001/XMLSchema-instance'
                            )
                        ),
                        'xmlns' => 'http://pace2020.com/epace/object'
                    ),
                    'photo' => array(
                        '_attributes' => array(
                            'xsi:nil' => array(
                                'value' => 'true',
                                'namespace' => 'http://www.w3.org/2001/XMLSchema-instance'
                            )
                        ),
                        'xmlns' => 'http://pace2020.com/epace/object'
                    ),
                    'jdfDeviceID' => array(
                        '_attributes' => array(
                            'xsi:nil' => array(
                                'value' => 'true',
                                'namespace' => 'http://www.w3.org/2001/XMLSchema-instance'
                            )
                        ),
                        'xmlns' => 'http://pace2020.com/epace/object'
                    ),
                    'ioID' => array(
                        '_attributes' => array(
                            'xsi:nil' => array(
                                'value' => 'true',
                                'namespace' => 'http://www.w3.org/2001/XMLSchema-instance'
                            )
                        ),
                        'xmlns' => 'http://pace2020.com/epace/object'
                    ),
                )
            )
        );

        return $this->sendParamsToServer(null, $params, '', $this->getMethodUrl('ReadObject'), array($this->getAuthHeader()));
    }

    public function readEstimate($id)
    {
        return $this->readObject('estimate', [
            'id' => $id
        ]);
    }

    public function readObject($objectType, $params)
    {
        $_params = [];
        foreach ($params as $key => $value) {
            $_params[$key] = [
                'xmlns' => 'http://pace2020.com/epace/object',
                $value
            ];
        }

        $operation = 'read' . ucfirst($objectType);
        $body = [
            $operation => [
                'xmlns' => 'urn://pace2020.com/epace/sdk/ReadObject',
                lcfirst($objectType) => $_params
            ]
        ];

        return $this->getObjectResponse($body, 'read', $objectType);
    }

    public function findObjects($objectType, $filter, $sort = null, $offset = null, $limit = null)
    {
        $method = 'find';
        $methodParams = [
            'xmlns' => 'urn://pace2020.com/epace/sdk/FindObjects',
            'in0' => $objectType,
            'in1' => $filter
        ];

        if ($sort) {
            $method = 'findAndSort';
            $methodParams['in2'] = $sort;
        }

        if ($offset || $limit) {
            $method = 'findSortAndLimit';
            if (!isset($methodParams['in2'])) {
                $methodParams['in2'] = '';
            }
            $methodParams['in3'] = (int)$offset;
            $methodParams['in4'] = (int)$limit;
        }

        $params = [
            $method => $methodParams
        ];

        $body = $this->sendParamsToServer(null, $params, '', $this->getMethodUrl('FindObjects'), array($this->getAuthHeader()));

        $responseNode = $body->children('urn://pace2020.com/epace/sdk/FindObjects')->{$method . 'Response'};
        if (!$responseNode) {
            $this->throwException('No response node found.', $body);
        }
        $out = $responseNode->children()->out;
        if (!$out) {
            $this->throwException('No "out" node found.', $body);
        }

        return (array)$out->string;
    }

    /**
     * @param string $type
     * @param string[] $keys
     * @param string[] $fields
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param string $userName
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAuditData($type, $keys, $fields, \DateTime $startDate, \DateTime $endDate, $userName, $limit = 1000, $offset = 0)
    {
        $params = [
            'getAuditData' => [
                'xmlns' => 'urn://pace2020.com/epace/sdk/FindObjects',
                'in0' => (string)$type,
                'in1' => [
                    'string' => (array)$keys
                ],
                'in2' => [
                    'string' => (array)$fields
                ],
                'in3' => $startDate->format('Y-m-d\TH:i:s.0000\Z'),
                'in4' => $endDate->format('Y-m-d\TH:i:s.0000\Z'),
                'in5' => (string)$userName,
                'in6' => (int)$limit,
                'in7' => (int)$offset
            ]
        ];

        $body = $this->sendParamsToServer(null, $params, '', $this->getMethodUrl('FindObjects'), [$this->getAuthHeader()]);
        return $this->parseResponseBody($body, 'FindObjects');
    }

    public function createEmployeeTime($employee, $startDate, $startTime, $stopDate = null, $stopTime = null, array $otherSettings = array())
    {
        $settings = array_merge(array(
            'employee' => $employee,
            'startDate' => (new DateTime($startDate))->format('Y-m-d\TH:i:s.0000\Z'),
            'startTime' => (new DateTime($startTime))->format('1970-01-01\TH:i:s.0000\Z'),
            'stopDate' => $stopDate ? (new DateTime($stopDate))->format('Y-m-d\TH:i:s.0000\Z') : null,
            'stopTime' => $stopTime ? (new DateTime($stopTime))->format('1970-01-01\TH:i:s.0000\Z') : null
        ), $otherSettings);

        return $this->createObject($settings, 'employeeTime');
    }

    public function createJob($customer, $description, array $otherSettings = array())
    {
        $settings = array_merge(array('customer' => $customer, 'description' => $description), $otherSettings);

        return $this->createObject($settings, 'job');
    }

    public function updateJob($job, $status, array $otherSettings = array())
    {
        $settings = array_merge(array('job' => $job, 'adminStatus' => $status), $otherSettings);

        return $this->updateObject($settings, 'job');
    }

    public function createJobProduct($jobId, $description, $qtyOrdered = 1, $salesCategory = 1, $otherSettings)
    {
        $settings = array_merge(array(
            'job' => $jobId,
            'description' => $description,
            'qtyOrdered' => $qtyOrdered,
            'salesCategory' => $salesCategory
        ), $otherSettings);

        return $this->createObject($settings, 'jobProduct');
    }

    public function getWsdl($method, $api = null)
    {
        $method = $this->getMethodUrl($method) . '?wsdl';
        /* @var Blackbox_Soap_Model_Api $api */
        if (!$api) {
            $api = $this->api;
        }

        $response = $api->sendXmlToServer('', '', $method, array($this->getAuthHeader()));

        if (!$response) {
            throw new Exception('No response. Method: "' . $method . '"');
        }

        $xml = simplexml_load_string($response);

        if ($xml === false) {
            $e = new Exception('Response is not valid xml. Method: "' . $method . '"');
            $e->response = $response;
            throw $e;
        }

        $children = $xml->children('wsdl', true);
        return $children;
    }

    public function logCallback($type, $content, $action, $url = null, $headers = null, $requestFileId = null)
    {
        $file = Mage::getModel('epace/event_file')->setData(array(
            'event_id' => $this->event->getId(),
            'type' => $type,
            'action' => $url,
            'content' => $content,
            'ext' => 'xml',
            'related_file_id' => $requestFileId
        ))->save();

        if ($type == Blackbox_Soap_Model_Api::LOG_TYPE_REQUEST) {
            return $file->getId();
        }
    }

    public function createObject($settings, $objectType)
    {
        $settingNodes = $this->settingsToNodes($settings);

        $params = array(
            'create' . ucfirst($objectType) => array(
                'xmlns' => 'urn://pace2020.com/epace/sdk/CreateObject',
                lcfirst($objectType) => $settingNodes
            )
        );

//        $result = $this->sendParamsToServer(null, $params, '', $this->getMethodUrl('CreateObject'), array($this->getAuthHeader()));
//        $responseNodeName = 'create' . ucfirst($objectType) . 'Response';
//        return (array)$this->api->xmlToArray($result->children('ns1', true)->$responseNodeName->children()->out->children('ns2', true));

        return $this->getObjectResponse($params, 'create', $objectType);
    }

    public function updateObject($settings, $objectType)
    {
        $settingNodes = $this->settingsToNodes($settings);

        $params = array(
            'update' . ucfirst($objectType) => array(
                'xmlns' => 'urn://pace2020.com/epace/sdk/UpdateObject',
                lcfirst($objectType) => $settingNodes
            )
        );

        return $this->getObjectResponse($params, 'update', $objectType);
    }

    public function renderFilters($filters, Blackbox_Epace_Model_Epace_AbstractObject $resource)
    {
        if (empty($filters)) {
            $idFieldName = $resource->getIdFieldName();
            $renderedFilters = "@$idFieldName = 1 or @$idFieldName != 1";
        } else {
            $renderedFilters = '';

            foreach ($filters as $filter) {
                switch ($filter['type']) {
                    case 'or' :
                    case 'and':
                        if (!empty($renderedFilters)) {
                            $renderedFilters .= ' ' . $filter['type'] . ' ';
                        }
                        $renderedFilters .= $this->_renderFilter($filter, $resource);
                        break;
                    default:
                        throw new \Exception('Unrecognized filter type.');
                }
            }
        }

        return $renderedFilters;
    }

    public function renderOrders($orders)
    {
        if (empty($orders)) {
            $renderedOrder = null;
        } else {
            $renderedOrder = [];
            foreach ($orders as $field => $direction) {
                switch ($direction) {
                    case 'DESC':
                        $descending = 'true';
                        break;
                    case 'ASC':
                        $descending = 'false';
                        break;
                    default:
                        throw new \Exception('Invalid sort direction: ' . $direction);
                }
                $renderedOrder['XPathDataSort'][] = [
                    'xmlns' => 'http://rpc.services.appbox.pace2020.com',
                    'descending' => $descending,
                    'xpath' => $this->_renderFieldName($field)
                ];
            }
        }

        return $renderedOrder;
    }

    protected function _renderFilter($filter, Blackbox_Epace_Model_Epace_AbstractObject $resource)
    {
        $definition = $resource->getDefinition();
        if (is_array($filter['value'])) {
            $conditionKeyMap = [
                'eq'            => '=',
                'neq'           => '!=',
                'gt'            => '>',
                'lt'            => '<',
                'gteq'          => '>=',
                'lteq'          => '<=',
            ];

            $functionConditionKeyMap = [
                'starts' => 'starts-with({{field}}, {{value}})',
                'ends' => 'ends-with({{field}}, {{value}})',
                'contains' => 'contains({{field}}, {{value}})'
            ];

            foreach ($filter['value'] as $k => $v) {
                if ($conditionKeyMap[$k]) {
                    return $this->_renderFieldName($filter['field']) . ' ' . $conditionKeyMap[$k] . ' ' . $this->_renderFilterValue($v, $definition[$filter['field']]);
                }
                if ($functionConditionKeyMap[$k]) {
                    return str_replace('{{value}}', $this->_renderFilterValue($v, $definition[$filter['field']]), str_replace('{{field}}', $this->_renderFieldName($filter['field']), $functionConditionKeyMap[$k]));
                }
                break;
            }

            throw new \Exception('Unable to render filters.');
        } else {
            return $this->_renderFieldName($filter['field']) . ' = ' . $this->_renderFilterValue($filter['value'], $definition[$filter['field']]);
        }
    }

    protected function _renderFieldName($field)
    {
        if (strpos($field, '@') === false && strpos($field, '/') === false) {
            return '@' . $field;
        } else {
            return $field;
        }
    }

    protected function _renderFilterValue($value, $type = null)
    {
        if ($value instanceof \DateTime) {
            return 'date( ' . $value->format('Y, m, d') . ' )';
        } else if (is_string($value) && !($type == 'int' && is_numeric($value))) {
            return '\'' . str_replace('\'', '\\\'', $value) . '\'';
        } else {
            return (string)$value;
        }
    }

    protected function getObjectResponse($params, $method, $objectType, $ns1 = null)
    {
        $result = $this->sendParamsToServer(null, $params, '', $this->getMethodUrl(ucfirst($method) . 'Object'), array($this->getAuthHeader()));
        return $this->parseResponseBody($result, $method . ucfirst($objectType), $ns1);
    }

    protected function parseResponseBody(SimpleXMLElement $body, $method, $ns1 = null)
    {
        $responseNodeName = $method . 'Response';

        if (is_null($ns1)) {
            $responseNode = $body->children('ns1', true)->$responseNodeName;
        } else {
            $responseNode = $body->children($ns1)->$responseNodeName;
        }
        if (!$responseNode) {
            $this->throwException('No response node found.', $body);
        }
        $out = $responseNode->children()->out;
        if (!$out) {
            $this->throwException('No "out" node found.', $body);
        }

        return (array)$this->api->xmlToArray($out->children('ns2', true));
    }

    protected function &settingsToNodes($settings)
    {
        $settingNodes = array();
        foreach ($settings as $node => $value) {
            if (!$value) {
                continue;
            }
            $settingNodes[$node] = array(
                'xmlns' => 'http://pace2020.com/epace/object',
                $value
            );
        }

        return $settingNodes;
    }

    protected function sendParamsToServer($headerParams, $bodyParams, $action, $url = null, $headers = null, $api = null)
    {
        /* @var Blackbox_Soap_Model_Api $api */
        if (!$api) {
            $api = $this->api;
        }

        $response = $api->sendParamsToServer($headerParams, $bodyParams, $action, $url, $headers);

        if (!$response) {
            throw new Exception('No response. Method: "' . $url . '"');
        }

        $xml = simplexml_load_string($response);

        if ($xml === false) {
            $e = new Exception('Response is not valid xml. Method: "' . $url . '"');
            $e->response = $response;
            throw $e;
        }

        $children = $xml->children('soap', true);
        if ($children) {
            $body = $children->Body;

            if (isset($body->Fault)) {
                $this->throwException($body->Fault->children()->faultstring, $body);
            }

            return $body;
        }
    }

    protected function getAuthHeader()
    {
        return 'Authorization: Basic ' . $this->auth;
    }

    protected function getMethodUrl($method)
    {
        if ($this->company) {
            return '/rpc/company:' . $this->company . '/services/' . $method;
        }
        return '/rpc/services/' . $method;
    }

    protected function throwException($message, $response) {
        $e = new Blackbox_Epace_Model_Exception($message);
        $e->setResponse($response);
        throw $e;
    }
}