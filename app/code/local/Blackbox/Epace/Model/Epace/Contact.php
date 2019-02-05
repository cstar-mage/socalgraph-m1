<?php

class Blackbox_Epace_Model_Epace_Contact extends Blackbox_Epace_Model_Epace_AbstractObject
{
    /**
     * @var Blackbox_Epace_Model_Epace_Customer
     */
    protected $customer = null;

    protected function _construct()
    {
        $this->_init('Contact', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Customer|bool
     */
    public function getCustomer()
    {
        if (is_null($this->customer)) {
            $this->customer = false;
            if ($this->getData('customer')) {
                $customer = Mage::getModel('efi/customer')->load($this->getData('customer'));
                if ($customer->getId()) {
                    $this->customer = $customer;
                }
            }
        }

        return $this->customer;
    }

    public function setCustomer(Blackbox_Epace_Model_Epace_Customer $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'firstName' => '',
            'lastName' => '',
            'email' => '',
            'mobilePhoneNumber' => '',
            'companyName' => '',
            'address1' => '',
            'address2' => '',
            'city' => '',
            'state' => '',
            'zip' => '',
            'country' => '',
            'customer' => '',
            'residential' => 'bool',
            'active' => 'bool',
            'prospect' => 'bool',
            'doNotCall' => 'bool',
            'doNotEmail' => 'bool',
            'needsInfo' => 'bool',
            'crm' => 'bool',
            'defaultCurrency' => '',
            'autoUpdate' => 'bool',
            'jobContact' => 'bool',
            'failedGPSLookup' => 'bool',
            'metroAreaForced' => 'bool',
            'useAlternateText' => 'bool',
            'imUserName' => '',
            'dsfShipTo' => 'bool',
            'dsfUser' => 'bool',
            'altAutoUpdate' => 'bool',
            'altBill' => 'bool',
            'billTo' => 'bool',
            'shipTo' => 'bool',
            'globalContact' => 'bool',
            'stateKey' => '',
        ];
    }
}