<?php

class Epace_Exception extends Mage_Exception
{
    protected $response = null;

    public function setResponse($response)
    {
        $this->response = $response;
    }


    public function getResponse() {
        return $this->response;
    }
}

class Blackbox_Epace_Helper_Api extends Mage_Core_Helper_Abstract
{
    const JOB_STATUS_OPEN = 'O';
    const JOB_STATUS_CLOSED = 'C';

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

    public function findObjects($in0, $in1)
    {
        $params = array(
            'find' => array(
                'xmlns' => 'urn://pace2020.com/epace/sdk/FindObjects',
                'in0' => $in0,
                'in1' => $in1
            )
        );

        $result = $this->sendParamsToServer(null, $params, '', $this->getMethodUrl('FindObjects'), array($this->getAuthHeader()));
        if (!$result) {
            $this->throwException('Wrong response', $result);
        }
        $responseNode = $result->children('ns1', true)->findResponse;
        if (!$responseNode) {
            $this->throwException('No response node found.', $result);
        }
        $out = $responseNode->children()->out;
        if (!$out) {
            $this->throwException('No "out" node found.', $result);
        }

        $result = [];
        foreach ($out->string as $node)
        {
            $result[] = (string)$node;
        }

        return $result;
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
            throw new Exception('No resonse. Method: "' . $method . '"');
        }

        $xml = simplexml_load_string($response);

        if ($xml === false) {
            $e = new Exception('Response is not valid xml. Method: "' . $method . '"');
            $e->respnse = $response;
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
                $objectType => $settingNodes
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
                $objectType => $settingNodes
            )
        );

        return $this->getObjectResponse($params, 'update', $objectType);
    }

    protected function getObjectResponse($params, $method, $objectType)
    {
        $result = $this->sendParamsToServer(null, $params, '', $this->getMethodUrl(ucfirst($method) . 'Object'), array($this->getAuthHeader()));
        $responseNodeName = $method . ucfirst($objectType) . 'Response';

        $responseNode = $result->children('ns1', true)->$responseNodeName;
        if (!$responseNode) {
            $this->throwException('No response node found.', $result);
        }
        $out = $responseNode->children()->out;
        if (!$out) {
            $this->throwException('No "out" node found.', $result);
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
            throw new Exception('No resonse. Method: "' . $url . '"');
        }

        $xml = simplexml_load_string($response);

        if ($xml === false) {
            $e = new Exception('Response is not valid xml. Method: "' . $url . '"');
            $e->respnse = $response;
            throw $e;
        }

        $children = $xml->children('soap', true);
        if ($children) {
            $body = $children->Body;

            if (isset($body->Fault)) {
                throw new Exception($body->Fault->children()->faultstring);
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
        $e = new Epace_Exception($message);
        $e->setResponse($response);
        throw $e;
    }
}