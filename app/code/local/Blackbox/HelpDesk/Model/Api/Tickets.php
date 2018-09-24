<?php

class Blackbox_HelpDesk_Model_Api_Tickets extends Zendesk_Zendesk_Model_Api_Tickets
{
    protected $transactionLevel = 0;

    public function show($id)
    {
        $response = $this->_call("tickets/$id.json", null, 'GET', null, true);

        return (isset($response['ticket']) ? $response['ticket'] : null);
    }

    public function create($data)
    {
        $response = $this->_call('tickets.json', null, 'POST', $data, true);

        return (isset($response['ticket']) ? $response['ticket'] : null);
    }

    public function update($id, $data)
    {
        $response = $this->_call("tickets/$id.json", null, 'PUT', $data, true);

        return (isset($response['ticket']) ? $response['ticket'] : null);
    }

    public function redactComment($ticketId, $commentId, $data)
    {
        $response = $this->_call("tickets/$ticketId/comments/$commentId/redact.json", null, 'PUT', $data, true);

        return (isset($response['comment']) ? $response['comment'] : null);
    }

    public function makeCommentPrivate($ticketId, $commentId)
    {
        $response = $this->_call("tickets/$ticketId/comments/$commentId/make_private.json", null, 'PUT', [], true);

        return (isset($response['comment']) ? $response['comment'] : null);
    }

    public function comments($ticketId, $sortOrder = null)
    {
        if ($sortOrder) {
            $data = ['sort_order' => $sortOrder];
        } else {
            $data = null;
        }
        $response = $this->_call("tickets/$ticketId/comments.json", null, 'GET', $data, true);

        return (isset($response['comments']) ? $response['comments'] : null);
    }

    public function incremental($startTime)
    {
        return $this->_call('incremental/tickets.json', ['start_time' => $startTime], 'GET', null, true);
    }

    public function events($startTime)
    {
        return $this->_call('incremental/ticket_events.json', ['start_time' => $startTime], 'GET', null, true);
    }

    public function beginTransaction()
    {
        $this->transactionLevel++;
    }

    public function commit()
    {
        if (--$this->transactionLevel < 0) {
            $this->transactionLevel = 0;
        }
    }

    public function rollBack()
    {
        if (--$this->transactionLevel < 0) {
            $this->transactionLevel = 0;
        }
    }

    public function getTransactionLevel()
    {
        return $this->transactionLevel;
    }
}