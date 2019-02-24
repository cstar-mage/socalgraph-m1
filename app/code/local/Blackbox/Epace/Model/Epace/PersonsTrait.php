<?php

trait Blackbox_Epace_Model_Epace_PersonsTrait
{
    /**
     * @var Blackbox_Epace_Model_Epace_Customer
     */
    protected $customer = null;

    /**
     * @var Blackbox_Epace_Model_Epace_SalesPerson
     */
    protected $salesPerson = null;

    /**
     * @var Blackbox_Epace_Model_Epace_CSR
     */
    protected $csr = null;

    /**
     * @return Blackbox_Epace_Model_Epace_Customer|bool
     */
    public function getCustomer()
    {
        return $this->_getObject('customer', 'customer', 'efi/customer', true);
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Customer $customer
     * @return $this
     */
    public function setCustomer(Blackbox_Epace_Model_Epace_Customer $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_SalesPerson|bool
     */
    public function getSalesPerson()
    {
        return $this->_getObject('salesPerson', 'salesPerson', 'efi/salesPerson', true);
    }

    /**
     * @param Blackbox_Epace_Model_Epace_SalesPerson $salesPerson
     * @return $this
     */
    public function setSalesPerson(Blackbox_Epace_Model_Epace_SalesPerson $salesPerson)
    {
        $this->salesPerson = $salesPerson;

        return $this;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_CSR|bool
     */
    public function getCSR()
    {
        return $this->_getObject('csr', 'csr', 'efi/cSR', true);
    }

    /**
     * @param Blackbox_Epace_Model_Epace_CSR $csr
     * @return $this
     */
    public function setCSR(Blackbox_Epace_Model_Epace_CSR $csr)
    {
        $this->csr = $csr;

        return $this;
    }
}