<?php

abstract class Blackbox_Epace_Model_Resource_Epace_Collection extends Varien_Data_Collection
{
    /**
     * Model name
     *
     * @var string
     */
    protected $_model;

    /**
     * Resource model name
     *
     * @var string
     */
    protected $_resourceModel;

    /**
     * Resource instance
     *
     * @var Blackbox_Epace_Model_Epace_AbstractObject
     */
    protected $_resource;

    protected $_renderedFilters = '';

    protected $_renderedOrder = null;

    protected $_renderedLimit = null;

    protected $_renderedOffset = null;

    /**
     * Whether orders are rendered
     *
     * @var bool
     */
    protected $_isOrdersRendered = false;

    /**
     * @var Blackbox_Epace_Model_Epace_Cache
     */
    private $_cache = null;

    public function __construct($cache = null)
    {
        $this->_construct();
        if (empty($this->_model)) {
            throw new \Exception('Collection model should be initialized in _construct method.');
        }
        if ($cache instanceof Blackbox_Epace_Model_Epace_Cache) {
            $this->_cache = $cache;
        }
    }

    protected abstract function _construct();

    /**
     * Standard resource collection initialization
     *
     * @param string $model
     * @param Mage_Core_Model_Resource_Db_Abstract $resourceModel
     * @return $this
     */
    protected function _init($model, $resourceModel = null)
    {
        $this->setModel($model);
        if (is_null($resourceModel)) {
            $resourceModel = $model;
        }
        $this->setResourceModel($resourceModel);
        return $this;
    }

    /**
     * Set model name for collection items
     *
     * @param string $model
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    public function setModel($model)
    {
        if (is_string($model)) {
            $this->_model = $model;
            $this->setItemObjectClass(Mage::getConfig()->getModelClassName($model));
        }
        return $this;
    }

    /**
     *  Set resource model name for collection items
     *
     * @param string $model
     */
    public function setResourceModel($model)
    {
        $this->_resourceModel = $model;
    }

    /**
     *  Retrieve resource model name
     *
     * @return string
     */
    public function getResourceModelName()
    {
        return $this->_resourceModel;
    }

    /**
     * Get resource instance
     *
     * @return Blackbox_Epace_Model_Epace_AbstractObject
     */
    public function getResource()
    {
        if (empty($this->_resource)) {
            $this->_resource = Mage::getModel($this->getResourceModelName());
        }
        return $this->_resource;
    }

    /**
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        $this->_renderFilters();
        $this->_renderOrders();
        $this->_renderLimit();
        $itemIds = $this->_loadIds();

        foreach ($itemIds as $id) {
            if ($this->_cache) {
                $item = $this->_cache->load($this->_itemObjectClass, $id);
            } else {
                /** @var Blackbox_Epace_Model_Epace_AbstractObject $item */
                $item = $this->getNewEmptyItem();
                $item->load($id);
            }
            $this->addItem($item);
        }

        $this->_setIsLoaded();

        return $this;
    }

    /**
     * @return int[]
     */
    public function loadIds()
    {
        $this->_renderFilters();
        $this->_renderOrders();
        $this->_renderLimit();
        return $this->_loadIds();
    }

    public function getSize()
    {
        if (is_null($this->_totalRecords)) {
            $this->_renderFilters();
            $this->_renderedOrder = null;
            $this->_renderedLimit = null;
            $this->_renderedOffset = null;
            $this->_totalRecords = count($this->_loadIds());
        }
        return $this->_totalRecords;
    }

    public function clearFilters()
    {
        $this->_filters = [];

        return $this;
    }

    protected function _renderFilters()
    {
        if ($this->_isFiltersRendered) {
            return $this;
        }

        if (empty($this->_filters)) {
            $idFieldName = $this->getResource()->getIdFieldName();
            $this->_renderedFilters = "@$idFieldName = 1 or @$idFieldName != 1";
        } else {
            $this->_renderedFilters = '';

            foreach ($this->_filters as $filter) {
                switch ($filter['type']) {
                    case 'or' :
                    case 'and':
                        if (!empty($this->_renderedFilters)) {
                            $this->_renderedFilters .= ' ' . $filter['type'] . ' ';
                        }
                        $this->_renderedFilters .= $this->_renderFilter($filter);
                        break;
                    default:
                        throw new \Exception('Unrecognized filter type.');
                }
            }
        }
        $this->_isFiltersRendered = true;
        return $this;
    }

    protected function _renderFilter($filter)
    {
        $definition = $this->getResource()->getDefinition();
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

    protected function _renderOrders()
    {
        if (empty($this->_orders)) {
            $this->_renderedOrder = null;
        } else {
            $this->_renderedOrder = [];
            foreach ($this->_orders as $field => $direction) {
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
                $this->_renderedOrder['XPathDataSort'][] = [
                    'xmlns' => 'http://rpc.services.appbox.pace2020.com',
                    'descending' => $descending,
                    'xpath' => $this->_renderFieldName($field)
                ];
            }
        }
        $this->_isOrdersRendered = true;

        return $this;
    }

    protected function _renderLimit()
    {
        if($this->_pageSize){
            $this->_renderedLimit = $this->_pageSize;
            $this->_renderedOffset = ($this->_curPage - 1) * $this->_pageSize;
        } else {
            $this->_renderedLimit = null;
            $this->_renderedOffset = null;
        }

        return $this;
    }

    protected function _loadIds()
    {
        return $this->getResource()->getApi()->findObjects(ucfirst($this->getResource()->getObjectType()), $this->_renderedFilters, $this->_renderedOrder, $this->_renderedOffset, $this->_renderedLimit);
    }
}