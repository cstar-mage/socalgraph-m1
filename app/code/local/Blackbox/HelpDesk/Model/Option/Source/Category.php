<?php

class Blackbox_HelpDesk_Model_Option_Source_Category extends Blackbox_HelpDesk_Model_Option_Source_Abstract
{
    protected $options = false;

    protected function _getEmptyLabel()
    {
        return 'Please Select Request Category';
    }

    protected function _getItems()
    {
        $result = [];
        $options = $this->getOptions();
        if ($options) {
            foreach ($options as $option) {
                $result[$option['value']] = $option['name'];
            }
        }

        return $result;
    }

    public function getOptions()
    {
        if ($this->options === false) {
            $fields = $this->getTicketResource()->getCustomFields();
            $categoryField = null;
            foreach ($fields as $field) {
                if ($field['title'] == 'Category') {
                    $this->options = $field['custom_field_options'];
                    break;
                }
            }
        }

        return $this->options;
    }

    /**
     * @return Blackbox_HelpDesk_Model_Resource_Ticket
     */
    protected function getTicketResource()
    {
        return Mage::getResourceModel('helpdesk/ticket');
    }
}