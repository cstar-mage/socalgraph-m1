<?php

/**
 * @method Blackbox_HelpDesk_Model_Resource_Comment getResource()
 *
 * Class Blackbox_HelpDesk_Model_Resource_Comment_Collection
 */
class Blackbox_HelpDesk_Model_Resource_Comment_Collection extends Blackbox_HelpDesk_Model_Resource_Zendesk_Collection_Abstract
{
    protected $count = null;

    protected function _construct()
    {
        $this->_init('helpdesk/comment');
    }

    public function setTicketIdFilter($ticketId)
    {
        if ($filter = $this->getFilter('ticket_id')) {
            $filter->setData('value', $ticketId);
        } else {
            $this->addFilter('ticket_id', $ticketId);
        }
    }

    public function loadData()
    {
        if (!($ticketIdFilter = $this->getFilter('ticket_id'))) {
            Mage::throwException('Cannot load comments without specified ticket');
        }

        $ticketId = $ticketIdFilter->getValue();
        if (!is_numeric($ticketId) && !is_string($ticketId)) {
            Mage::throwException('Wrong ticket id');
        }

        $data = $this->getResource()->_getReadAdapter()->comments($ticketId);
        if (!is_null($data)) {
            foreach ($data as $row) {
                if ($this->_filterItem($row)) {
                    $item = $this->getNewEmptyItem();
                    $item->setData($row);
                    $item->setTicketId($ticketId);
                    $this->addItem($item);
                }
            }
        }

        $this->count = count($data);

        return $this;
    }

    public function getSize()
    {
        return $this->count;
    }

    /**
     * Add collection filter
     *
     * @param string $field
     * @param string $value
     * @param string $type and|or|string
     */
    public function addFilter($field, $value)
    {
        $filter = new Varien_Object(); // implements ArrayAccess
        $filter['field']   = $field;
        $filter['value']   = $value;

        $this->_filters[] = $filter;
        $this->_isFiltersRendered = false;
        return $this;
    }

    public function addFieldToFilter($field, $filter)
    {
        return $this->addFilter($field, $filter);
    }

    public function setIsLoaded($flag)
    {
        return $this->_setIsLoaded($flag);
    }

    /**
     * @param array $item
     */
    protected function _filterItem($item)
    {
        foreach ($this->_filters as $filter) {
            if ($filter['field'] == 'ticket_id') {
                continue;
            }

            if (is_array($filter['value'])) {
                $found = false;
                foreach ($filter['value'] as $value) {
                    if ($value == $item[$filter['field']]) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    return false;
                }
            } else {
                if ($filter['value'] != $item[$filter['field']]) {
                    return false;
                }
            }
        }
        return true;
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        $number = (($this->getCurPage() - 1) * $this->getPageSize()) + 1;
        foreach ($this->_items as $item) {
            $item->setNumber($number++);
        }
        return $this;
    }
}