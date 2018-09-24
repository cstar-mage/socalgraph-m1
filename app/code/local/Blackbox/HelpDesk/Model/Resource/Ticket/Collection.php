<?php

/**
 * @method Blackbox_HelpDesk_Model_Resource_Ticket getResource()
 *
 * Class Blackbox_HelpDesk_Model_Resource_Ticket_Collection
 */
class Blackbox_HelpDesk_Model_Resource_Ticket_Collection extends Blackbox_HelpDesk_Model_Resource_Zendesk_Collection_Abstract
{
    protected $_count;
    protected $_search;
    protected $_viewColumns = array();
    protected static $_excludedColumns = array('score');

    public $users = array();

    protected function _construct()
    {
        $this->_init('helpdesk/ticket');
        $this->_search = new Zendesk_Zendesk_Model_Search( Zendesk_Zendesk_Model_Search::TYPE_TICKET );
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     * @return $this
     */
    public function addCustomerFilter($customer)
    {
        if (!is_object($customer)) {
            $customer = Mage::getModel('customer/customer')->load($customer);
        }
        return $this->addFieldToFilter('requester_id', Mage::helper('helpdesk')->getZendeskRequesterId($customer));
    }

    public function addFieldToFilter($fieldName, $condition = null) {
        if(is_numeric($condition) || is_string($condition) || is_array($condition)) {

            $searchFields = array();

            switch($fieldName) {
                case 'subject':
                    $searchFields[] = array(
                        'field' => 'subject',
                        'value' => '"'.$condition.'"'
                    );
                    break;
                case 'requester':
                    if (is_array($condition)) {
                        break;
                    }

                    $searchFields[] = array(
                        'field' => 'requester',
                        'value' => '*' . $condition,
                    );

                    $searchFields[] = array(
                        'field' => 'requester',
                        'value' => $condition . '*',
                    );
                    break;
                case 'requester_id':
                case 'tags':
                case 'status':
                case 'priority':
                case 'group':
                case 'assignee':
                    if (is_array($condition)) {
                        foreach ($condition as $operator => $value) {
                            $searchFields[] = array(
                                'field' => $fieldName,
                                'value' => $value,
                                'operator' => $operator
                            );
                        }
                    } else {
                        $searchFields[] = array(
                            'field' => $fieldName,
                            'value' => $condition
                        );
                    }
                    break;
                case 'type':
                    $searchFields[] = array(
                        'field' => 'ticket_type',
                        'value' => $condition
                    );
                    break;
                case 'id':
                    $searchFields[] = array(
                        'field'     =>  '',
                        'value'     =>  $condition,
                        'operator'  =>  ''
                    );
                    break;
                case 'created_at':
                case 'updated_at':
                    $fieldName  = substr($fieldName, 0, -3);

                    if( isset($condition['from']) AND Mage::helper('zendesk')->isValidDate($condition['from']) ) {
                        $value = Mage::helper('zendesk')->getFormatedDataForAPI( $condition['from'] );
                        $searchFields[] = array(
                            'field'     =>  $fieldName,
                            'value'     =>  $value,
                            'operator'  =>  '>'
                        );
                    }

                    if( isset($condition['to']) AND Mage::helper('zendesk')->isValidDate($condition['to']) ) {
                        $value = Mage::helper('zendesk')->getFormatedDataForAPI( $condition['to'] );
                        $searchFields[] = array(
                            'field'     =>  $fieldName,
                            'value'     =>  $value,
                            'operator'  =>  '<'
                        );
                    }
                    break;
            }
            foreach ($searchFields as $field) {
                $operator = isset($field['operator']) ? $field['operator'] : ":";
                $value = isset($field['value']) ? $field['value'] : "none";
                $this->_search->addField( new Zendesk_Zendesk_Model_Search_Field($field['field'], $value, $operator));
            }
        }

        return $this;
    }

    public function addAnswersToSelect()
    {
//        $commentsSelect = $this->getConnection()->select()->from($this->getTable('helpdesk/comment'), array(
//            'ticket_id' => 'ticket_id',
//            'qty' => new Zend_Db_Expr('count(id) - 1')
//        ))->group('ticket_id');
//
//        $this->getSelect()->join($commentsSelect, 'id=ticket_id', array('answers' => 'qty'));
        return $this;
    }

    public function loadData($printQuery = false, $logQuery = false)
    {
        require_once (Mage::getModuleDir('', 'Zendesk_Zendesk') . DS . 'lib' . DS .'functions.php');

        $searchQuery = array(
            'query' => $this->_search->getString(),
        );

        $params = array_merge($searchQuery, $this->params);

        $all = $this->getResource()->_getReadAdapter()->search($params);

        // Set the users for this collection
        $this->users = $all['users'];

        $emails = array_column($this->users, 'email', 'id');

        if (!is_null($all)) {
            foreach ($all['results'] as $ticket) {
                $ticket['requester_email'] = (isset($emails[$ticket['requester_id']]) ? $emails[$ticket['requester_id']] : '');

                $item = $this->getNewEmptyItem();
                $item->setData($ticket);
                $this->addItem($item);
            }

            $this->_count = $all['count'];

            Mage::unregister('zendesk_tickets_all');
            Mage::register('zendesk_tickets_all', $all['count']);
        } else {
            $this->_count = 0;
            Mage::unregister('zendesk_tickets_all');
        }

        return $this;
    }

    /**
     * Retrieve collection all items count
     *
     * @return int
     */
    public function getSize() {
        if (is_null($this->_count)) {
            $searchQuery = array(
                'query' => $this->_search->getString(),
            );

            $params = array_merge($searchQuery, [
                'per_page' => 1,
                'page' => 1
            ]);

            $all = $this->getResource()->_getReadAdapter()->search($params);

            if (is_null($all)) {
                $this->_count = 0;
            } else {
                $this->_count = $all['count'];
            }
        }
        return (int) $this->_count;
    }
}