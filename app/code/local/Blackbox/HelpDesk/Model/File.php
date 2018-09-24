<?php

/**
 * @method Blackbox_HelpDesk_Model_File setFileName(string $value)
 * @method string getFileName()
 * @method Blackbox_HelpDesk_Model_File setCommentId(string $value)
 * @method string getCommentId()
 * @method Blackbox_HelpDesk_Model_File setPath(string $value)
 * @method string getPath()
 *
 * Class Blackbox_HelpDesk_Model_File
 */
class Blackbox_HelpDesk_Model_File extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('helpdesk/file');
    }

    /**
     * @param Blackbox_HelpDesk_Model_Comment $comment
     * @return Blackbox_HelpDesk_Model_File
     */
    public function setComment(Blackbox_HelpDesk_Model_Comment $comment)
    {
        $this->setCommentId($comment->getId());
        return $this->setData('comment', $comment);
    }

    public function getComment()
    {
        if (!($comment = $this->getData('comment'))) {
            $comment = Mage::getModel('helpdesk/comment')->load($this->getCommentId());
            $this->setData('comment', $comment);
        }
        return $comment;
    }

    public function upload($token = null)
    {
        /** @var Blackbox_HelpDesk_Model_Api_Uploads $api */
        $api = Mage::getSingleton('helpdesk/api_uploads');

        $result = $api->uploads($this->getFileName(), $this->getPath(), $token);
        if (is_array($result)) {
            $this->setData($result['upload']['attachment']);
            return $result['upload']['token'];
        }
        return false;
    }

    protected function _beforeSave()
    {
        if ($this->isObjectNew()) {
            if ($this->hasData('comment')) {
                if (!$this->getCommentId()) {
                    $this->setCommentId($this->getComment()->getId());
                }
            }
        }
        if (!$this->getCommentId()) {
            Mage::throwException('Cannot save file without specified comment.');
        }
        if (!$this->getPath()) {
            Mage::throwException('Cannot save file without path.');
        }
        if (!$this->getFileName()) {
            Mage::throwException('Cannot save file without name.');
        }
        return parent::_beforeSave();
    }
}