<?php

class Blackbox_HelpDesk_Block_Customer_Ticket_Comment extends Mage_Core_Block_Template
{
    /** @var Blackbox_HelpDesk_Model_Comment  */
    protected $_comment;
    protected $_fileRenderers = array();

    /**
     * @param Blackbox_HelpDesk_Model_Comment $comment
     * @return $this
     */
    public function setComment(Blackbox_HelpDesk_Model_Comment $comment)
    {
        $this->_comment = $comment;
        return $this;
    }

    /**
     * @return Blackbox_HelpDesk_Model_Comment
     */
    public function getComment()
    {
        return $this->_comment;
    }

    /**
     * @param array $renderers
     * @return $this
     */
    public function setFileRenderers(array $renderers)
    {
        $this->_fileRenderers = $renderers;
        return $this;
    }

    /**
     * @param Blackbox_HelpDesk_Model_File $file
     * @return string
     */
    public function getFileHtml(Blackbox_HelpDesk_Model_File $file)
    {
        return $this->_getFileRenderer($file)
            ->setFile($file)
            ->toHtml();
    }

    public function getPosterName()
    {
        $comment = $this->getComment();
        if ($comment->getCustomerId() == Mage::getSingleton('customer/session')->getCustomerId()) {
            return 'You';
        } else if ($comment->getIsFromOp()) {
            return $comment->getCustomer()->getName();
        } else {
            return 'Support';
        }
    }

    /**
     * @param $mask
     * @param Blackbox_HelpDesk_Model_File $file
     * @return bool
     */
    protected function _matchFileType($mask, Blackbox_HelpDesk_Model_File $file)
    {
        return fnmatch($mask, $file->getFileName());
    }

    /**
     * @param Blackbox_HelpDesk_Model_File $file
     * @return Blackbox_HelpDesk_Block_Customer_Ticket_Comment_File
     */
    protected function _getFileRenderer(Blackbox_HelpDesk_Model_File $file)
    {
        foreach ($this->_fileRenderers as $mask => $renderer) {
            if ($mask == 'default') {
                continue;
            }
            if ($this->_matchFileType($mask, $file)) {
                return $renderer;
            }
        }
        if (isset($this->_fileRenderers['default'])) {
            return $this->_fileRenderers['default'];
        }
        Mage::throwException('No renderers found for file ' . $file->getName());
    }
}