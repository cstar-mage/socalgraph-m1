<?php

/**
 * @method Blackbox_HelpDesk_Model_Comment setBody(string $value)
 * @method string getBody()
 * @method Blackbox_HelpDesk_Model_Comment setTicketId(int $value)
 * @method int getTicketId()
 * @method Blackbox_HelpDesk_Model_Comment setAuthorId(string $value)
 * @method string getAuthorId()
 * @method string getCreatedAt()
 * @method string getUpdatedAt()
 * @method Blackbox_HelpDesk_Model_Comment setNumber(int $value)
 * @method int getNumber()
 *
 * Class Blackbox_HelpDesk_Model_Comment
 */
class Blackbox_HelpDesk_Model_Comment extends Mage_Core_Model_Abstract
{
    /** @var Blackbox_HelpDesk_Model_Resource_File_Collection */
    protected $_files = false;

    protected function _construct()
    {
        $this->_init('helpdesk/comment');
    }

    /**
     * @param Blackbox_HelpDesk_Model_Ticket $ticket
     * @return $this
     */
    public function setTicket(Blackbox_HelpDesk_Model_Ticket $ticket)
    {
        $this->setTicketId($ticket->getId());
        return $this->setData('ticket', $ticket);
    }

    /**
     * @return Blackbox_HelpDesk_Model_Ticket
     */
    public function getTicket()
    {
        if (!($ticket = $this->getData('ticket'))) {
            $ticket = Mage::getModel('helpdesk/ticket')->load($this->getTicketId());
            $this->setData('ticket', $ticket);
        }
        return $ticket;
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     * @return $this
     */
    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $this->setAuthorId(Mage::helper('helpdesk')->getZendeskRequesterId($customer));
        return $this->setData('customer', $customer);
    }

    /**
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (!($customer = $this->getData('customer'))) {
            $customer = Mage::helper('helpdesk')->getCustomerByZendeskUserId($this->getAuthorId());
            $this->setData('customer', $customer);
        }
        return $customer;
    }

    public function getCustomerId()
    {
        return $this->getCustomer()->getId();
    }

    /**
     * @param Blackbox_HelpDesk_Model_Resource_File_Collection $files
     * @return $this
     */
    public function setFile(Blackbox_HelpDesk_Model_Resource_File_Collection $files)
    {
        $this->_files = $files;
        return $this;
    }

    /**
     * @param bool $load
     * @return Blackbox_HelpDesk_Model_Resource_File_Collection
     */
    public function getFiles($load = true)
    {
        if ($load) {
            if (!$this->_files) {
                $this->_files = Mage::getResourceModel('helpdesk/file_collection')
                    ->addFieldToFilter('comment_id', $this->getId())
                    ->addFieldToFilter('ticket_id', $this->getTicketId());
                foreach ($this->_files as $file) {
                    $file->setComment($this);
                }
            }
            return $this->_files;
        } else {
            return Mage::getResourceModel('helpdesk/file_collection')
                ->addFieldToFilter('comment_id', $this->getId())
                ->addFieldToFilter('ticket_id', $this->getTicketId());
        }
    }

    /**
     * @param Blackbox_HelpDesk_Model_File $file
     * @return $this
     */
    public function addFile(Blackbox_HelpDesk_Model_File $file)
    {
        $this->getFiles()->addItem($file);
        $file->setComment($this);
        return $this;
    }

    public function getIsOp()
    {
        return $this->getTicket()->getComments()->getFirstItem()->getId() == $this->getId();
    }

    /**
     * @return bool
     */
    public function getIsFromOp()
    {
        return $this->getAuthorId() == $this->getTicket()->getRequesterId();
    }

    public function saveFiles()
    {
        if ($this->_files) {
            $token = null;
            $uploads = [];
            /** @var Blackbox_HelpDesk_Model_File $file */
            foreach ($this->_files as $file) {
                if (!$file->getId() && $file->getFileName() && $file->getPath()) {
                    $token = $file->upload();
                    if ($token) {
                        $uploads[] = $token;
                    }
                }
            }
            $this->setData('uploads', $uploads);
        }
    }

    protected function _beforeSave()
    {
        if ($this->isObjectNew()) {
            if ($this->hasData('ticket')) {
                if (!$this->getAuthorId()) {
                    $this->setAuthorId($this->getTicket()->getRequesterId());
                }
                if (!$this->getTicketId()) {
                    $this->setTicketId($this->getTicket()->getId());
                }
            }
            $this->saveFiles();
        }
        if (!$this->getTicketId()) {
            Mage::throwException('Cannot create comment without specified ticket.');
        }
        if (!$this->getBody()) {
            Mage::throwException('Cannot create comment without text.');
        }
        return parent::_beforeSave();
    }
}