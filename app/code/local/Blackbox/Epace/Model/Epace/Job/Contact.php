<?php

class Blackbox_Epace_Model_Epace_Job_Contact extends Blackbox_Epace_Model_Epace_Job_AbstractChild
{
    /** @var Blackbox_Epace_Model_Epace_Contact */
    protected $contact = null;

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
        return $this->_getObject('contact', 'contact', 'efi/contact', true);
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Contact $contact
     * @return $this
     */
    public function setContact(Blackbox_Epace_Model_Epace_Contact $contact)
    {
        $this->contact = $contact;

        return $this;
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'job' => 'int',
            'contact' => '',
            'billTo' => 'bool',
            'shipTo' => 'bool',
            'contactType' => 'string',
        ];
    }
}