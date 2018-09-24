<?php

class Blackbox_HelpDesk_Model_Option_Source_Type extends Blackbox_HelpDesk_Model_Option_Source_Abstract
{
    protected function _getEmptyLabel()
    {
        return 'Please Select Request Type';
    }

    protected function _getItems()
    {
        return $this->_getTicket()->getTypes();
    }
}