<?php

/**
 * @method Blackbox_HelpDesk_Model_Resource_File getResource()
 *
 * Class Blackbox_HelpDesk_Model_Resource_File_Collection
 */
class Blackbox_HelpDesk_Model_Resource_File_Collection extends Blackbox_HelpDesk_Model_Resource_Zendesk_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('helpdesk/file');
    }

    public function addFieldToFilter($field, $filter)
    {
        return $this->addFilter($field, $filter);
    }

    public function loadData($printQuery = false, $logQuery = false)
    {
        if (!($ticketIdFilter = $this->getFilter('ticket_id'))) {
            Mage::throwException('Cannot load attachments without specified ticket');
        }

        $commentIdFilter = $this->getFilter('comment_id');

        $data = [];
        if ($commentIdFilter) {
            /** @var Blackbox_HelpDesk_Model_Comment $comment */
            $comment = Mage::getModel('helpdesk/comment');
            $comment->setTicketId($ticketIdFilter['value'])->load($commentIdFilter['value']);
            $data = (array)$comment->getAttachments();
        } else {
            /** @var Blackbox_HelpDesk_Model_Resource_Comment_Collection $comments */
            $comments = Mage::getResourceModel('helpdesk/comment_collection');
            $comments->setTicketIdFilter($ticketIdFilter['value']);
            foreach ($comments as $_comment) {
                $data = array_merge($data, (array)$_comment->getAttachments());
            }
        }

        foreach ($data as $row) {
            $item = $this->getNewEmptyItem();
            $item->setData($row);
            if (isset($comment)) {
                $item->setComment($comment);
            }
            $this->addItem($item);
        }
    }
}