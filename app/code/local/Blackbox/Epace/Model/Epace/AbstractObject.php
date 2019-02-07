<?php

abstract class Blackbox_Epace_Model_Epace_AbstractObject extends Varien_Object
{
    /**
     * @var Blackbox_Epace_Helper_Api
     */
    protected $_api;

    /**
     * @var string
     */
    protected $_objectType;

    public function __construct()
    {
        parent::__construct();
        if (empty($this->_objectType)) {
            throw new \Exception('Object type should be initialized in _construct method.');
        }
    }

    public function getObjectType()
    {
        return $this->_objectType;
    }

    public function load($id)
    {
        try {
            $data = $this->getApi()->readObject($this->_objectType, [
                $this->getIdFieldName() => $id
            ]);
            $this->setData($this->_prepareLoadedData($data));
        } catch (Epace_Exception $e) {
            $this->unsetData();
        }
        $this->_hasDataChanges = false;

        return $this;
    }

    protected function _init($objectType, $idFieldName)
    {
        $this->_objectType = $objectType;
        $this->setIdFieldName($idFieldName);
    }

    /**
     * @return Blackbox_Epace_Helper_Api
     */
    public function getApi()
    {
        if (!$this->_api) {
            $this->_api = Mage::helper('epace/api');
        }
        return $this->_api;
    }

    /**
     * @return array
     */
    public abstract function getDefinition();

    protected function _prepareLoadedData(array $data)
    {
        $definition = $this->getDefinition();

        foreach ($data as $key => $value)
        {
            switch ($definition[$key]) {
                case 'bool':
                    if ($value == 'true') {
                        $data[$key] = true;
                    } else if ($value == 'false') {
                        $data[$key] = false;
                    } else {
                        throw new Exception('Data type does not match with definition. Expected boolean.');
                    }
                    break;
                case 'int':
                    if (!is_int($data[$key]) && !is_numeric($data[$key])) {
                        throw new Exception('Data type does not match with definition. Expected integer.');
                    }
                    $data[$key] = (int)$data[$key];
                    break;
            }
        }

        return $data;
    }

    protected function _underscore($name)
    {
        return lcfirst($name); // use keys from epace as is
    }
}