<?php

class Blackbox_Epace_Model_Epace_Job_Contact extends Blackbox_Epace_Model_Epace_Job_AbstractChild
{
    protected function _construct()
    {
        $this->_init('JobContact', 'id');
    }

    /**
     * @return int
     */
    public function getContactId()
    {
        return $this->getData('contact');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Contact|bool
     */
    public function getContact()
    {
        return $this->_getObject('contact', 'contact', 'efi/contact');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Contact $contact
     * @return $this
     */
    public function setContact(Blackbox_Epace_Model_Epace_Contact $contact)
    {
        return $this->_setObject('contact', $contact);
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'job' => 'string',
            'contact' => '',
            'billTo' => 'bool',
            'shipTo' => 'bool',
            'contactType' => 'string',
        ];
    }
}