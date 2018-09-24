<?php

abstract class Blackbox_HelpDesk_Model_Resource_Zendesk_Collection_Abstract extends Varien_Data_Collection
{
    protected $_model;
    protected $_resourceModel;
    protected $_resource;
    protected $params = [];

    public function __construct()
    {
        $this->_construct();
    }

    protected abstract function _construct();

    /**
     * Standard resource collection initialization
     *
     * @param string $model
     * @param Mage_Core_Model_Resource_Db_Abstract $resourceModel
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
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
     * @return $this
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

    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        $this->_beforeLoad();
        parent::load($printQuery, $logQuery);
        $this->_setIsLoaded(true);
        $this->_afterLoad();
        return $this;
    }

    /**
     * Add select order
     *
     * @param   string $field
     * @param   string $direction
     * @return  Varien_Data_Collection_Db
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->params['sort_by'] = $field;
        $this->params['sort_order'] = strtolower($direction);
        return $this;
    }

    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        return $this->setOrder($field, $direction);
    }

    /**
     * Set collection page start and records to show
     * Backward compatibility with EAV collection
     *
     * @param integer $pageNum
     * @param integer $pageSize
     * @return $this
     */
    public function setPage($pageNum, $pageSize)
    {
        $this->setCurPage($pageNum)
            ->setPageSize($pageSize);
        return $this;
    }

    /**
     * Set current page
     *
     * @param   int $page
     * @return  $this
     */
    public function setCurPage($page)
    {
        $this->params['page'] = $page;
        return parent::setCurPage($page);
    }

    /**
     * Set collection page size
     *
     * @param   int $size
     * @return  $this
     */
    public function setPageSize($size)
    {
        $this->params['per_page'] = $size;
        return parent::setPageSize($size);
    }

    /**
     * Get resource instance
     *
     * @return Blackbox_HelpDesk_Model_Resource_Zendesk_Abstract
     */
    public function getResource()
    {
        if (empty($this->_resource)) {
            $this->_resource = Mage::getResourceModel($this->getResourceModelName());
        }
        return $this->_resource;
    }

    protected function _beforeLoad()
    {
        return $this;
    }

    protected function _afterLoad()
    {
        return $this;
    }
}