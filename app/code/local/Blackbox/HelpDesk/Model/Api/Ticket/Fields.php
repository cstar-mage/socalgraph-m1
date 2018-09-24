<?php

class Blackbox_HelpDesk_Model_Api_Ticket_Fields extends Zendesk_Zendesk_Model_Api_Abstract
{
    public function listing()
    {
        $response = $this->_call("ticket_fields.json", null, 'GET', null, true);

        return (isset($response['ticket_fields']) ? $response['ticket_fields'] : null);
    }

    public function show($id)
    {
        $response = $this->_call("ticket_fields/$id.json", null, 'GET', null, true);

        return (isset($response['ticket_fields']) ? $response['ticket_fields'] : null);
    }
}