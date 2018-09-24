<?php

class Blackbox_Epace_Block_Adminhtml_Epace_View_Statistics_Default extends Blackbox_Epace_Block_Adminhtml_Epace_View_Statistics_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('blackbox/epace/view/statistics/default.phtml');
    }

    public function getEventData()
    {
        $result = array();
        foreach ($this->_eventData as $label => $value) {
            $result[htmlentities(ucfirst($label)) . ':'] = htmlentities($value);
        }
        return $result;
    }
}