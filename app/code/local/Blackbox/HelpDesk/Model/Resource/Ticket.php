<?php

/**
 * @method Blackbox_HelpDesk_Model_Api_Tickets _getReadAdapter()
 * @method Blackbox_HelpDesk_Model_Api_Tickets _getWriteAdapter()
 *
 * Class Blackbox_HelpDesk_Model_Resource_Ticket
 */
class Blackbox_HelpDesk_Model_Resource_Ticket extends Blackbox_HelpDesk_Model_Resource_Zendesk_Abstract
{
    protected static $customFields = false;

    protected $requesterFields = [
        'email' => 'requester',
        'zendesk_requester_id' => 'requester_id',
        'name' => 'requester_name'
    ];

    protected function _construct()
    {
        $this->saveFields = [
            'external_id',
            'type',
            'subject',
            'raw_subject',
            'priority',
            'status',
            'recipient',
            'requester_id',
            'submitter_id',
            'assignee_id',
            'organization_id',
            'group_id',
            'collaborator_ids',
            'collaborators',
            'follower_ids',
            'forum_topic_id',
            'problem_id',
            'due_at',
            'tags',
            'custom_fields',
            'via_followup_source_id',
            'macro_ids',
            'ticket_form_id',
            'brand_id',

            'requester',
            'requester_name',
        ];

        $this->_init('helpdesk/api_tickets');
    }

    /**
     * @param Blackbox_HelpDesk_Model_Ticket $object
     * @param int $id
     * @param string $field
     */
    public function load($object, $id, $field = null)
    {
        if (is_null($field)) {
            $field = $this->getIdFieldName();
        }
        if ($field == $this->getIdFieldName()) {
            $data = $this->_getReadAdapter()->show($id);
        } else {
            $data = $this->_getReadAdapter()->search([
                'query' => [
                    $field => $id
                ],
                'per_page' => 1,
                'sort_by' => $field
            ]);
            if (isset($data['tickets'][0])) {
                $data = $data['tickets'][0];
            } else {
                $data = null;
            }
        }

        if (is_null($data)) {
            $object->setData([]);
        } else {
            $this->_assignData($object, $data);
        }
    }

    /**
     * @param Blackbox_HelpDesk_Model_Ticket $object
     * @return $this
     */
    public function save($object)
    {
        if ($object->getId()) {
            $this->_update($object);
        } else {
            $this->_create($object);
        }
        return $this;
    }

    public function getCustomFields()
    {
        if (self::$customFields === false) {
            /** @var Blackbox_HelpDesk_Model_Api_Ticket_Fields $api */
            $api = Mage::getModel('helpdesk/api_ticket_fields');
            self::$customFields = $api->listing();
        }
        return self::$customFields;
    }

    /**
     * @param Blackbox_HelpDesk_Model_Ticket $object
     */
    protected function _create($object)
    {
        $this->_prepareRequesterData($object);

        $data = $this->_prepareDataForSave($object);

        $comment = $object->getComments()->getFirstItem();
        $data['comment'] = [
            'value' => $comment->getBody()
        ];
        if ($comment->saveFiles());
        if ($comment->getUploads()) {
            $data['comment']['uploads'] = $comment->getUploads();
        }

        $ticket = array(
            'ticket' => $data
        );

        $response = $this->_getWriteAdapter()->create($ticket);
        if ($response['id']) {
            $object->setId($response['id']);
            $comment->setDataChanges(false);
        } else {
            Mage::throwException('Could not create the ticket.');
        }
    }

    /**
     * @param Blackbox_HelpDesk_Model_Ticket $object
     */
    protected function _update($object)
    {
        $data = $this->_prepareDataForSave($object, true);

        $ticket = array(
            'ticket' => $data
        );

        $response = $this->_getWriteAdapter()->update($object->getId(), $ticket);
        if ($response['id']) {
            $object->setId($response['id']);
        } else {
            Mage::throwException('Could not update the ticket.');
        }
    }

    /**
     * @param Blackbox_HelpDesk_Model_Ticket $object
     */
    protected function _prepareRequesterData($object)
    {
        if ($customer = $object->getCustomer()) {
            foreach ($this->requesterFields as $customerField => $field)
            {
                if (!$object->getData($field)) {
                    $method = 'get' . $this->_camelize($customerField);
                    $object->setData($field, $customer->$method());
                }
            }
        }

        if (!$object->getData('requester_id')) {
            // See if the requester already exists in Zendesk
            try {
                $user = Mage::getModel('zendesk/api_requesters')->find($object->getData('requester_email'));
            } catch (Exception $e) {
                // Continue on, no need to show an alert for this
                $user = null;
            }

            if($user) {
                $object->setData('requester_id', $user['id']);
            } else {
                // Create the requester as they obviously don't exist in Zendesk yet
                try {
                    // First check if the requesterName has been provided, since we need that to create a new
                    // user (but if one exists already then it doesn't need to be filled out in the form)
                    if(strlen($object->getData('requester_name')) == 0) {
                        throw new Exception('Requester name not provided for new user');
                    }

                    // All the data we need seems to exist, so let's create a new user
                    $user = Mage::getModel('zendesk/api_requesters')->create($object->getData('requester_email'), $object->getData('requester_name'));
                    $object->setData('requester_id', $user['id']);
                } catch(Exception $e) {
                    throw $e;
                }
            }
        }
    }
}