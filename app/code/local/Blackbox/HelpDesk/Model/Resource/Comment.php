<?php

/**
 * @method Blackbox_HelpDesk_Model_Api_Tickets _getReadAdapter()
 * @method Blackbox_HelpDesk_Model_Api_Tickets _getWriteAdapter()
 *
 * Class Blackbox_HelpDesk_Model_Resource_Comment
 */
class Blackbox_HelpDesk_Model_Resource_Comment extends Blackbox_HelpDesk_Model_Resource_Zendesk_Abstract
{
    protected function _construct()
    {
        $this->saveFields = [
            'body',
            'author_id',
            'uploads'
        ];

        $this->_init('helpdesk/api_tickets');
    }

    /**
     * @param Blackbox_HelpDesk_Model_Comment $object
     */
    public function save($object)
    {
        if (!$this->_validate($object)) {
            Mage::throwException('Cannot save comment without specified ticket');
        }
        if ($object->getId()) {
            $this->_update($object);
        } else {
            $this->_create($object);
        }
    }

    /**
     * @param Blackbox_HelpDesk_Model_Comment $object
     */
    public function load($object, $id, $field = null)
    {
        if (!$this->_validate($object)) {
            Mage::throwException('Cannot load comment without specified ticket');
        }

        $comments = $this->_getReadAdapter()->comments($object->getTicketId());

        if (is_null($comments)) {
            $object->setData([]);
        } else {
            foreach ($comments as $comment) {
                if ($comment['id'] == $id) {
                    $this->_assignData($object, $comment);
                    return;
                }
            }

            $object->setData([]);
        }
    }

    /**
     * @param Blackbox_HelpDesk_Model_Comment $object
     */
    protected function _create($object)
    {
        $data = [
            'ticket' => [
                'comment' => $this->_prepareDataForSave($object)
            ]
        ];

        $response = $this->_getWriteAdapter()->update($object->getTicketId(), $data);
        if ($response['id']) {
            $comments = $this->_getReadAdapter()->comments($object->getTicketId(), 'desc');
            foreach ($comments as $comment) {
                if ($comment['body'] == $object->getBody() && $comment['author_id'] == $object->getAuthorId()) {
                    $object->setData($comment);
                    break;
                }
            }
        } else {
            Mage::throwException('Could not create the comment.');
        }
    }

    /**
     * @param Blackbox_HelpDesk_Model_Comment $object
     */
    protected function _update($object)
    {
        if ($object->getOrigData('public') && !$object->getData('public')) {
            $this->_getWriteAdapter()->makeCommentPrivate($object->getTicketId(), $object->getId());
        }
        if ($object->getData('text')) {
            $this->_getWriteAdapter()->redactComment($object->getTicketId(), $object->getId(), [
                'text' => $object->getText()
            ]);
        }
    }

    protected function _validate($object)
    {
        return (bool)$object->getTicketId();
    }
}