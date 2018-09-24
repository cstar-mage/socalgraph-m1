<?php

class Blackbox_HelpDesk_TicketController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('customer/account/login');
            return $this;
        }

        return $this;
    }

    public function indexAction()
    {
        $this->_title('Support')->_title('My Tickets');
        $this->loadLayout()
            ->initLayoutMessages('customer/session')
            ->renderLayout();
    }

    public function createAction()
    {
        $ticket = Mage::getModel('helpdesk/ticket')->setData((array)Mage::getSingleton('customer/session')->getHelpDeskTicketData(true));
        if ($ticket->isEmpty()) {
            $ticket->setData([
                'email' => Mage::helper('contacts')->getUserEmail(),
                'telephone' => Mage::getSingleton('customer/session')->getCustomer()->getTelephone(),
                'name' => Mage::helper('contacts')->getUserName()
            ]);
        }
        Mage::register('current_helpdesk_ticket', $ticket);
        $this->_title('Support')->_title('New Ticket');
        $this->loadLayout()->initLayoutMessages('customer/session')->renderLayout();
    }

    public function createPostAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_redirect('*/*/create');
            return;
        }
        $data = $this->getRequest()->getPost('ticket');

        $data['text'] = "Name: {$data['name']}
Email: {$data['email']}
Telephone: {$data['telephone']}

Comment: {$data['text']}";

        unset($data['name']);
        unset($data['email']);
        unset($data['telephone']);

        /** @var Blackbox_HelpDesk_Model_Ticket $ticket */
        $ticket = Mage::getModel('helpdesk/ticket')->setData($data);
        $ticket->setCustomer(Mage::getSingleton('customer/session')->getCustomer());

        try {
            $files = $this->_getFiles();

            if (!empty($files)) {
                $ticket->setFiles($files);
            }

            $ticket->save();

            Mage::dispatchEvent('helpdesk_ticket_new', array('ticket' => $ticket));

            Mage::getSingleton('customer/session')->setLastTicket([
                'time' => time(),
                'id' => $ticket->getId()
            ]);

            $this->_redirect('*/*/view', array('ticket_id' => $ticket->getId()));
            return;
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('customer/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('customer/session')->addError('Cannot create ticket');
            Mage::logException($e);
        }

        Mage::getSingleton('customer/session')->setHelpDeskTicketData($this->getRequest()->getPost('ticket'));
        $this->_redirect('*/*/create');
    }

    public function closeAction()
    {
        $ticket = $this->_initTicket();
        if (!$ticket) {
            $this->_redirect('*/*/');
            return;
        }

        if (!$ticket->canClose()) {
            Mage::getSingleton('customer/session')->addError('Cant close this ticket.');
            $this->_redirect('*/*/view', array('ticket_id' => $ticket->getId()));
            return;
        }

        try {
            $ticket->setStatus($ticket::STATUS_CLOSED);
            $ticket->save();
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('customer/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('customer/session')->addError('Cannot close the ticket.');
            Mage::logException($e);
        }

        $this->_redirect('*/*/view', array('ticket_id' => $ticket->getId()));
    }

    public function viewAction()
    {
        $ticket = $this->_initTicket();
        if (!$ticket) {
            $lastTicket = Mage::getSingleton('customer/session')->getLastTicket();
            if (is_array($lastTicket) && $lastTicket['id'] == $this->getRequest()->getParam('ticket_id') && time() - $lastTicket['time'] < 15) {
                Mage::getSingleton('customer/session')->addSuccess('The ticket was created. Wait while it is being processed on zendesk server.');
            }
            $this->_redirect('*/*/');
            return;
        }

        $this->_title('Support')->_title('Ticket ' . $ticket->getId());
        $this->loadLayout()
            ->initLayoutMessages('customer/session')
            ->renderLayout();
    }

    public function addPostAction()
    {
        if (!$this->getRequest()->isPost()) {
            $ticketId = $this->getRequest()->getParam('ticket_id');
            if ($ticketId) {
                $this->_redirect('*/*/view', array('ticket_id' => $ticketId));
            } else {
                $this->_redirect('*/*/');
            }
            return;
        }
        $ticket = $this->_initTicket();
        if (!$ticket) {
            $this->_redirect('*/*/');
            return;
        }
        $session = Mage::getSingleton('customer/session');
        if ($ticket->getStatus() == $ticket::STATUS_CLOSED) {
            $session->addError('Unable to post in closed ticket.');
            $this->_redirect('*/*/view', array('ticket_id' => $ticket->getId()));
            return;
        }
        $data = $this->getRequest()->getPost('comment');

        try {
            /** @var Blackbox_HelpDesk_Model_Comment $comment */
            $comment = Mage::getModel('helpdesk/comment');
            $comment->setData($data);
            $comment->setTicket($ticket);
            $comment->setAuthorId(Mage::helper('helpdesk')->getZendeskRequesterId($session->getCustomer()));

            $files = $this->_getFiles();
            if (!empty($files)) {
                foreach ($files as $file) {
                    $comment->addFile($file);
                }
            }

            $comment->save();

            $session->addSuccess('Post was added.');
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
            $session->setHelpDeskCommentData($data);
        } catch (Exception $e) {
            $session->addError('Cannot add post.');
            $session->setHelpDeskCommentData($data);
            Mage::logException($e);
        }

        $this->_redirect('*/*/view', array('ticket_id' => $ticket->getId()));
    }

    protected function _getFiles()
    {
        return Mage::helper('helpdesk')->getUploadedFiles();
    }

    protected function _initTicket()
    {
        $ticketId = $this->getRequest()->getParam('ticket_id');
        if (!$ticketId) {
            return false;
        }
        $session = Mage::getSingleton('customer/session');
        /** @var Blackbox_HelpDesk_Model_Ticket $ticket */
        $ticket = Mage::getModel('helpdesk/ticket')->load($ticketId);
        if (!$ticket->getId() || $ticket->getRequesterId() != Mage::helper('helpdesk')->getZendeskRequesterId($session->getCustomer())) {
            $session->addError('This ticket no longer exists.');
            return false;
        }
        Mage::register('current_helpdesk_ticket', $ticket);
        return $ticket;
    }
}