<?php

class Blackbox_HelpDesk_Block_Customer_Ticket_Comment_File extends Mage_Core_Block_Template
{
    /** @var  Blackbox_HelpDesk_Model_File */
    protected $_file;

    public function setFile(Blackbox_HelpDesk_Model_File $file)
    {
        $this->_file = $file;
        return $this;
    }

    /**
     * @return Blackbox_HelpDesk_Model_File
     */
    public function getFile()
    {
        return $this->_file;
    }
}