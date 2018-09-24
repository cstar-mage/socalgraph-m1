<?php

class Blackbox_HelpDesk_Model_Resource_File extends Blackbox_HelpDesk_Model_Resource_Zendesk_Abstract
{
    protected function _construct()
    {
        $this->_init('helpdesk/api_uploads', 'id');
    }

    public function load($object, $id, $field = null)
    {
        if (!$this->_validate($object)) {
            Mage::throwException('Cannot load attachment without specified ticket and comment');
        }

        $comment = Mage::getModel('helpdesk/comment')->setTicketId($object->getTicketId())->load($object->getCommentId());
        if ($comment->getId() && $comment->getAttachments()) {
            foreach ($comment->getAttachments() as $attachment) {
                if ($attachment['id'] == $id) {
                    $object->setData($attachment);
                    $object->setComment($comment);
                    return;
                }
            }
        }

        $object->setData([]);
    }

    public function save($object)
    {
    }

    protected function _validate($object)
    {
        return (bool)$object->getTicketId() && (bool)$object->getCommentId();
    }
}