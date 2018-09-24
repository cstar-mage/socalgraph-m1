<?php

class Blackbox_Epace_Block_Adminhtml_Epace_View_Statistics_Abstract extends Mage_Adminhtml_Block_Template
{
    protected $_eventData = null;

    public function setEvent($event)
    {
        $this->_eventData = unserialize($event->getSerializedData());
        return parent::setEvent($event);
    }

    protected function _toHtml()
    {
        if (!$this->_eventData) {
            $this->setTemplate('blackbox/epace/view/statistics/error.phtml');
        }

        return parent::_toHtml();
    }
}