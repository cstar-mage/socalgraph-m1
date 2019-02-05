<?php


class Blackbox_Epace_Model_Epace_Estimate extends Blackbox_Epace_Model_Epace_AbstractObject
{
    /**
     * @var Blackbox_Epace_Model_Epace_Estimate_Status
     */
    protected $status = null;

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
     * @var Blackbox_Epace_Model_Epace_Job
     */
    protected $job = null;

    protected function _construct()
    {
        $this->_init('Estimate', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate_Status
     */
    public function getStatus()
    {
        if (is_null($this->status)) {
            $this->status = Mage::helper('epace/object')->load('efi/estimate_status', $this->getData('status'));
        }

        return $this->status;
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Estimate_Status $status
     * @return $this
     */
    public function setStatus(Blackbox_Epace_Model_Epace_Estimate_Status $status)
    {
        $this->status = $status;

        return $this;
    }

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

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate_Product[]
     */
    public function getProducts()
    {
        return $this->_getEstimateItems('efi/estimate_product_collection');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate_Quantity[]
     */
    public function getQuantities()
    {
        return $this->_getEstimateItems('efi/estimate_quantity_collection');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate_Part[]
     */
    public function getParts()
    {
        return $this->_getEstimateItems('efi/estimate_part_collection');
    }

    /**
     * @return bool
     */
    public function isConvertedToJob()
    {
        return $this->getData('status') == Blackbox_Epace_Model_Epace_Estimate_Status::STATUS_CONVERTED_TO_JOB;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job|bool
     */
    public function getJob()
    {
        if (is_null($this->job)) {
            $this->job = false;

            if ($this->isConvertedToJob() && $this->hasData('lastJob')) {
                $job = Mage::getModel('efi/job')->load($this->getData('lastJob'));
                if ($job->getId()) {
                    $this->job = $job;
                }
            }
        }

        return $this->job;
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'estimateNumber' => '',
            'priceSummaryLevel' => '',
            'fromCombo' => '',
            'salesPerson' => 'int',
            'csr' => 'int',
            'estimator' => '',
            'entryDate' => '',
            'entryTime' => '',
            'enteredBy' => '',
            'followUpDate' => '',
            'customer' => 'string',
            'customerProspectName' => '',
            'description' => '',
            'notes' => '',
            'status' => '',
            'rewardDate' => '',
            'lastJob' => '',
            'estimateRequest' => '',
            'shipToContact' => '',
            'billToContact' => '',
            'taxableCode' => '',
            'addCRMOpportunity' => '',
            'addCRMActivity' => '',
            'freightOnBoard' => '',
            'debug' => '',
            'altCurrency' => '',
            'altCurrencyRate' => '',
            'altCurrencyRateSource' => '',
            'altCurrencyRateSourceNote' => '',
            'forceQuotedPriceOnConvert' => '',
            'committedFromMetrix' => '',
            'allowVAT' => '',
            'repetitiveRuns' => '',
            'manufacturingLocation' => '',
            'highestEstimateVersion' => '',
            'autoAddQuoteLetter' => '',
            'lastChangedDate' => '',
            'lastChangedTime' => '',
            'lastChangedBy' => '',
            'totalParts' => '',
            'totalPages' => ''
        ];
    }

    protected function _getEstimateItems($collectionName)
    {
        /** @var Blackbox_Epace_Model_Resource_Epace_Collection $collection */
        $collection = Mage::getResourceModel($collectionName);
        /** @var Blackbox_Epace_Model_Epace_Estimate_AbstractChild $items */
        $items = $collection->addFilter('estimate', (int)$this->getId())->getItems();
        foreach ($items as $item) {
            $item->setEstimate($this);
        }

        return $items;
    }
}