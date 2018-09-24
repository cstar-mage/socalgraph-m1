<?php

class Blackbox_HelpDesk_Model_Option_Source_Priority extends Blackbox_HelpDesk_Model_Option_Source_Abstract
{
    protected function _getItems()
    {
        return $this->_getTicket()->getPriorities();
    }

    protected function _getEmptyLabel()
    {
        return 'Please Select Priority';
    }
}