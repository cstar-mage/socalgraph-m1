<?php

/**
 * @method string getStatus()
 * @method string getPriority()
 * @method Blackbox_HelpDesk_Model_Ticket setType(string $value)
 * @method string getType()
 * @method Blackbox_HelpDesk_Model_Ticket setSubject(string $value)
 * @method string getSubject()
 * @method Blackbox_HelpDesk_Model_Ticket setRequesterId(string $value)
 * @method string getRequesterId()
 * @method Blackbox_HelpDesk_Model_Ticket setRequester(string $value)
 * @method string getRequester()
 * @method Blackbox_HelpDesk_Model_Ticket setRequesterName(string $value)
 * @method string getRequesterName()
 * @method string getCreatedAt()
 * @method string getUpdatedAt()
 * @method Blackbox_HelpDesk_Model_Resource_Ticket _getResource()
 *
 * Class Blackbox_HelpDesk_Model_Ticket
 */
class Blackbox_HelpDesk_Model_Ticket extends Mage_Core_Model_Abstract
{
    const STATUS_NEW = 'new';
    const STATUS_OPEN = 'open';
    const STATUS_PENDING = 'pending';
    const STATUS_HOLD = 'hold';
    const STATUS_SOLVED = 'solved';
    const STATUS_CLOSED = 'closed';

    /** @var  Blackbox_HelpDesk_Model_Resource_Comment_Collection */
    protected $_comments = false;
    /** @var Blackbox_HelpDesk_Model_Comment */
    protected $_op = false;

    protected function _construct()
    {
        $this->_init('helpdesk/ticket');
    }

    /**
     * @return array
     */
    public function getStatuses()
    {
        return array(
            'new' => 'New',
            'open' => 'Open',
            'pending' => 'Pending',
            'hold' => 'Hold',
            'solved' => 'Solved',
            'closed' => 'Closed',
        );
    }

    /**
     * @return array
     */
    public function getPriorities()
    {
        return [
            'urgent' => 'Urgent',
            'high' => 'High',
            'normal' => 'Normal',
            'low' => 'Low'
        ];
    }

    public function getTypes()
    {
        return [
            'problem' => 'Problem',
            'incident' => 'Incident',
            'question' => 'Question',
            'task' => 'Task'
        ];
    }

    public function setStatus($status)
    {
        if (!isset($this->getStatuses()[$status])) {
            Mage::throwException('Wrong status');
        }
        return $this->setData('status', $status);
    }

    public function setPriority($priority)
    {
        if (!isset($this->getPriorities()[$priority])) {
            Mage::throwException('Wrong priority');
        }
        return $this->setData('priority', $priority);
    }

    /**
     * @return string
     */
    public function getStatusText()
    {
        return $this->getStatuses()[$this->getStatus()];
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     * @return $this
     */
    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $this->setRequesterId(Mage::helper('helpdesk')->getZendeskRequesterId($customer));
        $this->setRequesterName($customer->getName());
        $this->setRequester($customer->getEmail());
        return $this->setData('customer', $customer);
    }

    /**
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (!($customer = $this->getData('customer'))) {
            if ($this->getRequester()) {
                if (Mage::app()->getStore()->getId() == Mage_Core_Model_App::ADMIN_STORE_ID) {
                    $websites = Mage::app()->getWebsites(true);
                } else {
                    $websites = [Mage::app()->getStore()->getWebsite()];
                }
                /** @var Mage_Core_Model_Website $website */
                foreach ($websites as $website) {
                    $customer = Mage::getModel('customer/customer')->setWebsiteId($website->getId())->loadByEmail($this->getRequester());
                    if ($customer->getId()) {
                        $this->setData('customer', $customer);
                        break;
                    }
                }
            }
        }
        return $customer;
    }

    public function getRequester()
    {
        if (!$this->getData('requester')) {
            if ($this->getRequesterId()) {
                /** @var Zendesk_Zendesk_Model_Api_Users $api */
                $api = Mage::getSingleton('zendesk/api_users');
                $requester = $api->get($this->getRequesterId());
                if ($requester['id']) {
                    $this->setRequester($requester['email']);
                }
            }
        }
        return $this->getData('requester');
    }

    /**
     * @return string
     */
    public function getText()
    {
        if ($this->isObjectNew()) {
            return $this->getData('text');
        }
        return $this->getOpeningPost()->getBody();
    }

    /**
     * @param Blackbox_HelpDesk_Model_Resource_Comment_Collection $comments
     * @return $this
     */
    public function setComments(Blackbox_HelpDesk_Model_Resource_Comment_Collection $comments)
    {
        $this->_comments = $comments;
        return $this;
    }

    /**
     * @param bool $load
     * @return Blackbox_HelpDesk_Model_Resource_Comment_Collection
     */
    public function getComments($load = true)
    {
        if ($load) {
            if (!$this->_comments) {
                if ($this->getId()) {
                    $this->_comments = Mage::getResourceModel('helpdesk/comment_collection')
                        ->addFieldToFilter('ticket_id', $this->getId())
                        ->addOrder('id', 'ASC');
                    foreach ($this->_comments as $comment) {
                        $comment->setTicket($this);
                    }
                } else {
                    $this->_comments = Mage::getResourceModel('helpdesk/comment_collection');
                    $this->_comments->setIsLoaded(true);
                }
            }
            return $this->_comments;
        } else {
            return Mage::getResourceModel('helpdesk/comment_collection')
                ->addFieldToFilter('ticket_id', $this->getId())
                ->addOrder('id', 'ASC');
        }
    }

    /**
     * @return Blackbox_HelpDesk_Model_Comment
     */
    public function getOpeningPost()
    {
        return $this->getComments()->getFirstItem();
    }

    public function addComment(Blackbox_HelpDesk_Model_Comment $comment)
    {
        $this->getComments()->addItem($comment);
        $comment->setTicket($this);
        return $this;
    }

    public function canClose()
    {
        return $this->getStatus() != self::STATUS_CLOSED;
    }

    public function canPost()
    {
        return $this->getStatus() != self::STATUS_CLOSED;
    }

    public function hasSupportResponse()
    {
        foreach ($this->getComments() as $comment) {
            if (!$comment->getIsFromOp()) {
                return true;
            }
        }
        return false;
    }

    public function getCustomFieldValue($field)
    {
        $field = $this->getCustomField($field);
        if (!$field) {
            return false;
        }
        $id = $field['id'];

        if ($this->getData('custom_fields')) {
            foreach ($this->getData('custom_fields') as $customField) {
                if ($customField['id'] == $id) {
                    return $customField['value'];
                }
            }
        }

        return false;
    }

    public function setCustomFieldValue($title, $value)
    {
        $field = $this->getCustomField($title);
        if ($field) {
            $this->_data['custom_fields'][$field['id']] = $value;
        }
        return $this;
    }

    public function getCustomFieldText($title)
    {
        $field = $this->getCustomField($title);
        if (!$field) {
            return false;
        }

        if (isset($field['custom_field_options'])) {
            $value = $this->getCustomFieldValue($title);
            if (is_array($value)) {
                $result = [];
                foreach ($value as $v) {
                    $result[] = $this->_getOptionNameByValue($field['custom_field_options'], $value) ?: $field['value'];
                }
                return implode(', ', $result);
            } else {
                return $this->_getOptionNameByValue($field['custom_field_options'], $value) ?: $field['value'];
            }
        }

        return $field['value'];
    }

    protected function _getOptionNameByValue(&$options, $value)
    {
        foreach ($options as $option) {
            if ($value == $option['value']) {
                return $option['name'];
            }
        }
        return false;
    }

    public function getCustomField($title)
    {
        foreach ($this->_getResource()->getCustomFields() as $field) {
            if ($title == $field['title']) {
                return $field;
            }
        }
        return false;
    }

    protected function _beforeSave()
    {
        if ($this->isObjectNew()) {
            if (!$this->getData('text')) {
                Mage::throwException('Cannot create ticket without text.');
            }
            $comment = Mage::getModel('helpdesk/comment')
                ->setData(array(
                    'body' => $this->getData('text')
                ))
                ->setTicket($this);
            if ($this->getFiles()) {
                foreach ($this->getFiles() as $file) {
                    $comment->addFile($file);
                }
            }
            $this->addComment($comment);
        }
        return parent::_beforeSave();
    }

    protected function _afterSave()
    {
        if ($this->_comments) {
            foreach ($this->_comments as $comment) {
                $comment->save();
            }
        }
        return parent::_afterSave();
    }
}