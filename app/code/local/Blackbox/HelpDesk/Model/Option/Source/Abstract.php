<?php

abstract class Blackbox_HelpDesk_Model_Option_Source_Abstract implements Mage_Eav_Model_Entity_Attribute_Source_Interface
{
    public function getAllOptions($withEmpty = true)
    {
        $result = [];
        foreach ($this->getOptionHash($withEmpty) as $value => $label) {
            $result[$value] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $result;
    }

    public function getOptionHash($withEmpty = true)
    {
        $result = $this->_getItems();
        if ($withEmpty) {
            $result = array('' => $this->_getEmptyLabel()) + $result;
        }
        return $result;
    }

    public function getOptionText($value)
    {
        return $this->_getItems()[$value];
    }

    protected abstract function _getItems();

    abstract protected function _getEmptyLabel();

    /**
     * @return Blackbox_HelpDesk_Model_Ticket
     */
    protected function _getTicket()
    {
        return Mage::getSingleton('helpdesk/ticket');
    }
}