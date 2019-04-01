<?php

class Blackbox_CinemaCLoud_Block_SalesRep_Order_History_Filter extends Mage_Core_Block_Template
{
    protected $_collection = null;
    protected $_timePeriodFrom = false, $_timePeriodTo = false;
    protected $_connection = null;

    protected function _construct()
    {
        $this->setTemplate('blackbox/cinemacloud/salesrep/order/history/filter.phtml');
    }

    /**
     * Set collection for filter
     *
     * @param  Mage_Sales_Model_Resource_Order_Collection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;

        $this->_applyFilter();
        $this->_applySort();

        return $this;
    }

    /**
     * @return null|Mage_Sales_Model_Resource_Order_Collection
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    public function getStatusSelectHtml($class = '')
    {
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName('status')
            ->setId('order-status-filter')
            ->setClass($class)
            ->setExtraParams('onchange="updateFilter(this)"')
            ->setValue($this->getCurrentStatus())
            ->setOptions($this->getStatusOptions());

        return $select->getHtml();
    }

    public function getSortSelectHtml($class = '')
    {
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName('sort_by')
            ->setId('order-sort-by')
            ->setClass($class)
            ->setExtraParams('onchange="updateFilter(this)"')
            ->setValue($this->getCurrentSort())
            ->setOptions($this->getSortOptions());

        return $select->getHtml();
    }

    public function getPaymentStatusSelectHtml($class = '')
    {
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName('payment_status')
            ->setId('payment-status-filter')
            ->setClass($class)
            ->setExtraParams('onchange="updateFilter(this)"')
            ->setValue($this->getPaymentStatus())
            ->setOptions($this->getPaymentStatusOptions());

        return $select->getHtml();
    }

    public function getCurrentTimePeriod()
    {
        if ($this->getTimePeriodFrom() && $this->getTimePeriodTo()) {
            return $this->_prepareDateValue($this->getTimePeriodFrom()) . ' - ' . $this->_prepareDateValue($this->getTimePeriodTo());
        }
        return 'All';
    }

    public function getCurrentStatus()
    {
        return $this->getRequest()->getParam('status');
    }

    public function getCurrentSort()
    {
        return $this->getRequest()->getParam('sort_by', 'order_date');
    }

    public function getPaymentStatus()
    {
        return $this->getRequest()->getParam('payment_status');
    }

    public function getShippingStatus()
    {
        return $this->getRequest()->getParam('shipping_status');
    }

    public function getQuery()
    {
        return $this->getRequest()->getParam('q');
    }

    public function getCurrentSortDirection()
    {
        return $this->getRequest()->getParam('sort_direction', 'desc');
    }

    protected function _applyFilter()
    {
        $this->_applyStatusFilter();
        $this->_applyPaymentStatusFilter();
        $this->_applyQueryFilter();
    }

    protected function getTimePeriodFrom()
    {
        if ($this->_timePeriodFrom === false) {
            $this->_timePeriodFrom = $this->getRequest()->getParam('from');
        }
        return $this->_timePeriodFrom;
    }

    protected function getTimePeriodTo()
    {
        if ($this->_timePeriodTo === false) {
            $this->_timePeriodTo = $this->getRequest()->getParam('to');
        }
        return $this->_timePeriodTo;
    }

    protected function _prepareDateValue($value)
    {
        return date('m/d/Y', $value);
    }

    protected function _applyStatusFilter()
    {
        $status = $this->getCurrentStatus();

        if ($status) {
            $filter = $status;
            $this->getCollection()->addFieldToFilter('status', $filter);
        }
    }

    protected function _applyPaymentStatusFilter()
    {
        $status = $this->getPaymentStatus();

        if ($status) {
            $paymentStatusSelect = $this->_getConnection()->select()->from($this->_getTable('sales/invoice'), 'order_id')->where('state = ?', $status);

            $this->getCollection()->getSelect()->where('entity_id IN (' . $paymentStatusSelect . ')');
        }
    }

    protected function _applyQueryFilter()
    {
        $q = $this->getQuery();

        if ($q) {
            $addressSelect = $this->_getConnection()->select()
                ->from($this->_getTable('sales/order_address'), 'parent_id');
            foreach ([
                'postcode',
                'street',
                     ] as $field) {
                $addressSelect->orWhere($field . ' LIKE ?', '%%' . $q . '%%');
            }

            $this->getCollection()->getSelect()->where('entity_id IN (' . $addressSelect . ') OR increment_id LIKE ?', '%%' . $q . '%%');
        }
    }

    protected function _applySort()
    {
        switch($this->getCurrentSort()) {
            case 'order_date_asc':
                $sortBy = 'created_at';
                $sortDirection = 'asc';
                break;
            case 'order_date_desc':
                $sortBy = 'created_at';
                $sortDirection = 'desc';
                break;
            default:
                $sortBy = 'created_at';
                $sortDirection = 'desc';
        }

        $this->getCollection()->setOrder($sortBy, $sortDirection);
    }

    public function getSortOptions()
    {
        return array(
            'order_date_desc' => 'Order Date Desc',
            'order_date_asc' => 'Order Date Asc',
        );
    }

    public function getStatusOptions()
    {
        $statuses = Mage::getSingleton('sales/order_config')->getStatuses();
        return array('' => 'All') + $statuses;
    }

    public function getPaymentStatusOptions()
    {
        return ['' => 'All'] + Mage_Sales_Model_Order_Invoice::getStates();
    }

    public function getTimePeriodUrl($from = null, $to = null)
    {
        return $this->_getUrl(array('from' => $from, 'to' => $to));
    }

    public function getStatusUrl($value = null)
    {
        return $this->_getUrl(array('status' => $value));
    }

    public function getSortUrl($value = null)
    {
        return $this->_getUrl(array('sort_by' => $value));
    }

    public function getQueryUrl($value = null)
    {
        return $this->_getUrl(array('q' => $value));
    }

    protected function _getUrl($params) {
        $params['_current'] = true;
        return $this->getUrl('*/*/*/', $params);
    }

    /**
     * @return Zend_Db_Adapter_Abstract
     */
    protected function _getConnection()
    {
        if (!$this->_connection) {
            $this->_connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        }
        return $this->_connection;
    }

    protected function _getTable($table)
    {
        return Mage::getSingleton('core/resource')->getTableName($table);
    }
}