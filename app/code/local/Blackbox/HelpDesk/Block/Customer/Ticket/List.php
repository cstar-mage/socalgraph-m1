<?php

class Blackbox_HelpDesk_Block_Customer_Ticket_List extends Mage_Core_Block_Template
{
    public function _construct()
    {
        parent::_construct();

        $session = Mage::getSingleton('customer/session');
        $tickets = Mage::getResourceModel('helpdesk/ticket_collection')
            ->addCustomerFilter($session->getCustomer())
            ->setOrder('updated_at', 'DESC')
            ->addAnswersToSelect();

        $this->setItems($tickets);
    }

    /**
     * @return Blackbox_HelpDesk_Block_Customer_Ticket_List
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'helpdesk.tickets.pager')
            ->setCollection($this->getItems());
        $this->setChild('pager', $pager);
        $this->getItems()->load();
        return $this;
    }

    /**
     * Return ticket view url
     *
     * @param integer $tiecketId
     * @return string
     */
    public function getTicketViewUrl($tiecketId)
    {
        return $this->getUrl('support/ticket/view', array('ticket_id' => $tiecketId));
    }

    public function getCreateTicketUrl()
    {
        return $this->getUrl('support/ticket/create');
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/');
    }

    /**
     * @return bool
     */
    public function getIsOpenInNewWindow()
    {
        return false;
    }
}