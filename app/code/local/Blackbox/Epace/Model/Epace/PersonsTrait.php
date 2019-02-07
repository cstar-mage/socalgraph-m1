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
        if (is_null($this->customer)) {
            $this->customer = false;
            if ($this->getData('customer')) {
                $customer = Mage::helper('epace/object')->load('efi/customer', $this->getData('customer'));
                if ($customer->getId()) {
                    $this->customer = $customer;
                }
            }
        }

        return $this->customer;
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
        if (is_null($this->salesPerson)) {
            $this->salesPerson = false;
            if ($this->getData('salesPerson')) {
                $salesPerson = Mage::helper('epace/object')->load('efi/salesPerson', $this->getData('salesPerson'));
                if ($salesPerson->getId()) {
                    $this->salesPerson = $salesPerson;
                }
            }
        }

        return $this->salesPerson;
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
        if (is_null($this->csr)) {
            $this->csr = false;
            if ($this->getData('csr')) {
                $csr = Mage::helper('epace/object')->load('efi/cSR', $this->getData('csr'));
                if ($csr->getId()) {
                    $this->csr = $csr;
                }
            }
        }

        return $this->csr;
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