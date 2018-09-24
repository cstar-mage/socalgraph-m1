<?php

class Blackbox_HelpDesk_Block_Customer_Ticket_Create extends Mage_Core_Block_Template
{
    /**
     * @return Blackbox_HelpDesk_Model_Ticket
     */
    public function getTicket()
    {
        return Mage::registry('current_helpdesk_ticket');
    }

    public function getPrioritySelectHtml()
    {
        $options = Mage::getModel('helpdesk/option_source_priority')->getAllOptions(true);
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName('ticket[priority]')
            ->setId('ticket-priority-select')
            ->setClass('priority-select required-entry')
            ->setExtraParams('')
            ->setValue($this->getTicket()->getPriority())
            ->setOptions($options);

        return $select->getHtml();
    }

    public function getCategorySelectHtml()
    {
        $options = Mage::getModel('helpdesk/option_source_category')->getAllOptions(true);
        $options['']['params'] = [
            'disabled' => 'disabled',
            'hidden' => 'hidden'
        ];
        /** @var Blackbox_HelpDesk_Model_Ticket $ticketModel */
        $ticketModel = Mage::getModel('helpdesk/ticket');
        $field = $ticketModel->getCustomField('Category');
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName("ticket[custom_fields][{$field['id']}]")
            ->setId('ticket-category-select')
            ->setClass('input-text category-select required-entry')
            ->setExtraParams('')
            ->setValue($this->getTicket()->getCustomFieldValue('Category'))
            ->setOptions($options);

        return $select->getHtml();
    }

    public function getTypeSelectHtml()
    {
        $options = Mage::getModel('helpdesk/option_source_type')->getAllOptions(true);
        $options['']['params'] = [
            'disabled' => 'disabled',
            'hidden' => 'hidden'
        ];
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName('ticket[type]')
            ->setId('ticket-type-select')
            ->setClass('input-text type-select required-entry')
            ->setExtraParams('')
            ->setValue($this->getTicket()->getType())
            ->setOptions($options);

        return $select->getHtml();
    }
}