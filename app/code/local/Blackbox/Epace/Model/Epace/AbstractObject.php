<?php

abstract class Blackbox_Epace_Model_Epace_AbstractObject extends Varien_Object
{
    public static $debug = false;

    /**
     * @var Blackbox_Epace_Helper_Api
     */
    protected $_api;

    /**
     * @var string
     */
    protected $_objectType;

    private $_childItems = [];

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

    protected function _getObject($objectField, $dataField, $modelClassName, $useCache = false)
    {
        if (is_null($this->$objectField)) {
            $this->$objectField = false;
            if (!empty($this->getData($dataField))) {
                /** @var Blackbox_Epace_Model_Epace_AbstractObject $object */
                if ($useCache) {
                    $object = Mage::helper('epace/object')->load($modelClassName, $this->getData($dataField));
                } else {
                    $object = Mage::getModel($modelClassName)->load($this->getData($dataField));
                }
                if ($object->getId()) {
                    $this->$objectField = $object;
                } else if (self::$debug) {
                    throw new \Exception("Unable to load object {$object->getObjectType()} with id {$this->getData($dataField)} linked by {$this->getObjectType()} in field $objectField");
                }
            }
        }

        return $this->$objectField;
    }

    protected function _getChildItems($collectionName, $filters, callable $initCallback = null)
    {
        if (!isset($this->_childItems[$collectionName])) {
            /** @var Blackbox_Epace_Model_Resource_Epace_Collection $collection */
            $collection = Mage::getResourceModel($collectionName);
            foreach ($filters as $field => $value) {
                $collection->addFilter($field, $value);
            }
            $this->_childItems[$collectionName] = $collection->getItems();
            foreach ($this->_childItems[$collectionName] as $item) {
                call_user_func($initCallback, $item);
            }
        }

        return $this->_childItems[$collectionName];
    }
}