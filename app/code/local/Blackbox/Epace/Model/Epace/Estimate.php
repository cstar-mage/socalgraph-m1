<?php

/**
 * @method int getEstimateNumber()
 * @method string getPriceSummaryLevel()
 * @method string getMetrixID()
 * @method bool getFromCombo()
 * @method int getEstimator()
 * @method string getEntryDate()
 * @method string getEntryTime()
 * @method string getEnteredBy()
 * @method string getFollowUpDate()
 * @method string getCustomerProspectName()
 * @method string getDescription()
 * @method string getRewardDate()
 * @method bool getEstimateRequest()
 * @method bool getShipToContact()
 * @method bool getBillToContact()
 * @method int getTaxableCode()
 * @method bool getAddCRMOpportunity()
 * @method bool getAddCRMActivity()
 * @method string getFreightOnBoard()
 * @method bool getDebug()
 * @method string getAltCurrency()
 * @method float getAltCurrencyRate()
 * @method string getAltCurrencyRateSource()
 * @method string getAltCurrencyRateSourceNote()
 * @method bool getForceQuotedPriceOnConvert()
 * @method bool getCommittedFromMetrix()
 * @method bool getAllowVAT()
 * @method bool getRepetitiveRuns()
 * @method int getManufacturingLocation()
 * @method bool getHighestEstimateVersion()
 * @method bool getAutoAddQuoteLetter()
 * @method string getLastChangedDate()
 * @method string getLastChangedTime()
 * @method string getLastChangedBy()
 * @method int getTotalParts()
 * @method int getTotalPages()
 *
 * Class Blackbox_Epace_Model_Epace_Estimate
 */
class Blackbox_Epace_Model_Epace_Estimate extends Blackbox_Epace_Model_Epace_AbstractObject
{
    use Blackbox_Epace_Model_Epace_PersonsTrait;

    /**
     * @var Blackbox_Epace_Model_Epace_Estimate_Status
     */
    protected $status = null;

    /**
     * @var Blackbox_Epace_Model_Epace_Job
     */
    protected $job = null;

    protected function _construct()
    {
        $this->_init('Estimate', 'id');
    }

    public function getStatusId()
    {
        return $this->getData('status');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate_Status
     */
    public function getStatus()
    {
        return $this->_getObject('status', 'status', 'efi/estimate_status', true);
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
     * @return Blackbox_Epace_Model_Epace_Job[]
     */
    public function getJobs()
    {
        return $this->_getChildItems('efi/job_collection', [
            'altCurrencyRateSource' => 'Estimate',
            'altCurrencyRateSourceNote' => (int)$this->getId()
        ], function ($item) {
            if ($this->getId() == $item->getEstimateId()) {
                $item->setEstimate($this);
            }
        });
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job|bool
     */
    public function getLastJob()
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
            'prospectName' => '',
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
        return $this->_getChildItems($collectionName, [
            'estimate' => (int)$this->getId()
        ], function ($item) {
            $item->setEstimate($this);
        });
    }
}