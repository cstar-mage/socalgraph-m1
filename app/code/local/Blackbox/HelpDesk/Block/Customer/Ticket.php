<?php

class Blackbox_HelpDesk_Block_Customer_Ticket extends Mage_Core_Block_Template
{
    protected $_commentRenderers = array();
    protected $_fileRenderers = array();

    /**
     * @return Blackbox_HelpDesk_Model_Ticket
     */
    public function getTicket()
    {
        return Mage::registry('current_helpdesk_ticket');
    }

    /**
     * @param string $type
     * @param string $block
     * @param string $template
     * @return $this
     */
    public function addCommentRenderer($type, $block, $template)
    {
        $block = $this->getLayout()->createBlock($block);
        if ($block) {
            $block->setTemplate($template);
            $block->setParentBlock($this);
            $this->_commentRenderers[$type] = $block;
        }
        return $this;
    }

    /**
     * @param string $mask
     * @param string $block
     * @param string $template
     * @return $this
     */
    public function addFileRenderer($block, $template, ...$masks)
    {
        $block = $this->getLayout()->createBlock($block);
        if ($block) {
            $block->setTemplate($template);
            foreach ($masks as $mask) {
                $this->_fileRenderers[$mask] = $block;
            }
        }
        return $this;
    }

    /**
     * @param Blackbox_HelpDesk_Model_Comment $comment
     * @return string
     */
    public function getCommentHtml($comment)
    {
        return $this->_getCommentRenderer($this->_getCommentType($comment))
            ->setComment($comment)
            ->toHtml();
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }

    public function getCloseUrl()
    {
        return $this->getUrl('*/*/close', array('ticket_id' => $this->getTicket()->getId()));
    }

    public function getPostUrl()
    {
        return $this->getUrl('*/*/addPost', array('ticket_id' => $this->getTicket()->getId()));
    }

    /**
     * @param Blackbox_HelpDesk_Model_Comment $comment
     * @return string
     */
    protected function _getCommentType(Blackbox_HelpDesk_Model_Comment $comment)
    {
        return Mage::helper('helpdesk')->getCommentRenderType($comment);
    }

    protected function _getCommentRenderer($type)
    {
        if (isset($this->_commentRenderers[$type])) {
            return $this->_commentRenderers[$type];
        }
        if (isset($this->_commentRenderers['default'])) {
            return $this->_commentRenderers['default'];
        }
        Mage::throwException('No renderers found for comment.');
    }

    protected function _beforeToHtml()
    {
        foreach ($this->_commentRenderers as $renderer) {
            $renderer->setFileRenderers($this->_fileRenderers);
        }
        return parent::_beforeToHtml();
    }
}