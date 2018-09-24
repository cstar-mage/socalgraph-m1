<?php

class Blackbox_HelpDesk_Model_Cron
{
    const XML_PATH_CONFIG_UPDATE_TIME = 'helpdesk/zendesk/update_time';
    const NOTIFICATION_TYPE_ZENDESK_UPDATE = 2319354;

    /** @var Blackbox_Notification_Model_Validator */
    protected $validator;
    protected $processedTickets = [];

    public function __construct()
    {
        $this->validator = Mage::getModel('blackbox_notification/validator');
    }

    public function processUpdates()
    {
        $time = time();
        $lastUpdate = Mage::getStoreConfig(self::XML_PATH_CONFIG_UPDATE_TIME);
        if (!$lastUpdate) {
            Mage::getConfig()->saveConfig(self::XML_PATH_CONFIG_UPDATE_TIME, $time);
            return;
        }

        /** @var Blackbox_HelpDesk_Model_Api_Tickets $api */
        $api = Mage::getSingleton('helpdesk/api_tickets');

        $response = $api->events($lastUpdate);

        foreach ($response['ticket_events'] as $ticketEvent) {
            $this->_processTicketEvent($ticketEvent);
        }

        Mage::getConfig()->saveConfig(self::XML_PATH_CONFIG_UPDATE_TIME, $time);
    }

    protected function _processTicketEvent($ticketEvent)
    {
        if ($this->processedTickets[$ticketEvent['ticket_id']]) {
            return;
        }

        $comment = false;
        foreach ($ticketEvent['child_events'] as $event) {
            if ($event['event_type'] == 'Comment') {
                $comment = true;
                break;
            }
        }
        if (!$comment) {
            return;
        }


        /** @var Blackbox_HelpDesk_Model_Ticket $ticket */
        $ticket = Mage::getModel('helpdesk/ticket')->load($ticketEvent['ticket_id']);
        if (!$ticket->getId()) {
            $this->processedTickets[$ticketEvent['ticket_id']] = true;
            return;
        }
        if ($ticket->getRequesterId() == $ticketEvent['updater_id']) {
            return;
        }

        $this->processedTickets[$ticketEvent['ticket_id']] = true;

        $customer = $ticket->getCustomer();
        if ($customer && $customer->getId()) {
            $this->validator->processNotification(self::NOTIFICATION_TYPE_ZENDESK_UPDATE, $ticket, [
                'customer' => $customer,
                'ticket' => $ticket
            ]);
        }
    }
}