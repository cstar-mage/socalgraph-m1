<?php

/**
 * @method bool getAddPlanFromJobType()
 * @method bool getEpaceEstimate()
 * @method bool getBillPartsTogether()
 * @method string getDescription()
 * @method string getEnteredBy()
 * @method int getJob()
 * @method int getJobType()
 * @method int getTotalParts()
 * @method int getTerms()
 * @method float getAllowableOvers()
 * @method string getDateSetup()
 * @method string getTimeSetUp()
 * @method string getPromiseDate()
 * @method bool getScheduledShipDateForced()
 * @method bool getScheduledShipTimeForced()
 * @method string getContactFirstName()
 * @method string getContactLastName()
 * @method int getOversMethod()
 * @method int getShipInNameOf()
 * @method bool getNumbersGuaranteed()
 * @method bool getConvertingToJob()
 * @method int getJobOrderType()
 * @method int getComboJobPercentageCalculationType()
 * @method string getFreightOnBoard()
 * @method string getAltCurrency()
 * @method float getAltCurrencyRate()
 * @method string getAltCurrencyRateSource()
 * @method int getAltCurrencyRateSourceNote()
 * @method bool getCreatedFromAnsix12850()
 * @method bool getReadyToSchedule()
 * @method bool getUseLegacyPrintFlowFormatPrePress()
 * @method bool getUseLegacyPrintFlowFormatFinishing()
 * @method bool getJdfSubmitted()
 * @method float getJobValue()
 * @method bool getDestinationBasedTaxing()
 * @method bool getDsfCreditCardFinalized()
 * @method bool getInvoiceW2POrderAmount()
 * @method bool getInvoiceW2PShippingAmount()
 * @method bool getInvoiceW2PTaxAmount()
 * @method bool getInvoiceW2PHandlingAmount()
 * @method int getManufacturingLocation()
 * @method bool getTaxCategoryForced()
 * @method bool getComboDirty()
 * @method int getInvoiceLevelOptions()
 * @method float getAmountToInvoice()
 * @method bool getAmountToInvoiceForced()
 * @method int getQuantityOrdered()
 * @method bool getQuantityOrderedForced()
 * @method float getOriginalQuotedPrice()
 * @method bool getOriginalQuotedPriceForced()
 * @method int getSalesCategory()
 * @method bool getSalesCategoryForced()
 * @method string getInvoiceUOM()
 * @method bool getInvoiceUOMForced()
 * @method float getAmountInvoiced()
 * @method float getChangeOrderTotal()
 * @method float getFreightAmountTotal()
 * @method bool getExecuteSync()
 * @method int getComboTotal()
 * @method string getCurrentStatus()
 * @method mixed getEarliestProofDue()
 * @method mixed getEarliestProofShipDateTime()
 * @method float getTotalPriceAllParts()
 * @method float getQuantityRemaining()
 * @method float getQtyOrdered()
 * @method bool getScheduled()
 * @method bool getPrePressScheduled()
 * @method bool getFinishingScheduled()
 * @method bool getPromptForMultipleParts()
 * @method bool getPromptForMultipleProducts()
 * @method bool getBillPartsTogetherAttribute()
 * @method bool getBillPartOneOnlyAttribute()
 *
 * Class Blackbox_Epace_Model_Epace_Job
 */
class Blackbox_Epace_Model_Epace_Job extends Blackbox_Epace_Model_Epace_AbstractObject
{
    use Blackbox_Epace_Model_Epace_PersonsTrait;

    protected function _construct()
    {
        $this->_init('Job', 'job');
    }

    /**
     * @return int
     */
    public function getTypeId()
    {
        return $this->getData('jobType');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Type|false
     */
    public function getType()
    {
        return $this->_getObject('type', 'jobType', 'efi/job_type');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Job_Type $type
     * @return $this
     */
    public function setType(Blackbox_Epace_Model_Epace_Job_Type $type)
    {
        return $this->_setObject('type', $type);
    }

    public function getAdminStatusCode()
    {
        return $this->getData('adminStatus');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Status|bool
     */
    public function getAdminStatus()
    {
        return $this->_getObject('status', 'adminStatus', 'efi/job_status', true);
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Job_Status $status
     * @return $this
     */
    public function setAdminStatus(Blackbox_Epace_Model_Epace_Job_Status $status)
    {
        return $this->_setObject('status', $status);
    }

    /**
     * @return string
     */
    public function getPrevAdminStatusCode()
    {
        return $this->getData('prevAdminStatus');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Status|bool
     */
    public function getPrevAdminStatus()
    {
        return $this->_getObject('prevStatus', 'prevAdminStatus', 'efi/job_status', true);
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Job_Status $status
     * @return $this
     */
    public function setPrevAdminStatus(Blackbox_Epace_Model_Epace_Job_Status $status)
    {
        return $this->_setObject('prevStatus', $status);
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Quote
     */
    public function getQuote()
    {
        return $this->_getObject('quote', 'quoteNumber', 'efi/quote');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Quote $quote
     * @return $this
     */
    public function setQuote(Blackbox_Epace_Model_Epace_Quote $quote)
    {
        return $this->_setObject('quote', $quote);
    }

    /**
     * @return bool
     */
    public function isSourceEstimate()
    {
        return $this->getData('altCurrencyRateSource') == 'Estimate';
    }

    /**
     * @return int|string
     */
    public function getEstimateId()
    {
        if ($this->isSourceEstimate()) {
            return $this->getAltCurrencyRateSourceNote();
        } else {
            return '';
        }
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate
     */
    public function getEstimate()
    {
        if (!$this->_hasObjectField('estimate')) {
            $this->_setObject('estimate', false);

            if ($this->isSourceEstimate()) {
                $estimate = $this->_loadObject('efi/estimate', $this->getData('altCurrencyRateSourceNote'));
                if ($estimate->getId()) {
                    $this->_setObject('estimate', $estimate);
                }
            }
        }

        return $this->_getObjectField('estimate');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Estimate $estimate
     * @return $this
     */
    public function setEstimate(Blackbox_Epace_Model_Epace_Estimate $estimate)
    {
        return $this->_setObject('estimate', $estimate);
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Ship_Via
     */
    public function getShipVia()
    {
        return $this->_getObject('shipVia', 'shipVia', 'efi/ship_via', true);
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Product[]
     */
    public function getProducts()
    {
        return $this->_getJobItems('efi/job_product_collection');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Contact[]
     */
    public function getJobContacts()
    {
        return $this->_getJobItems('efi/job_contact_collection');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Invoice[]
     */
    public function getInvoices()
    {
        return $this->_getJobItems('efi/invoice_collection');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Shipment[]
     */
    public function getShipments()
    {
        return $this->_getJobItems('efi/job_shipment_collection');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Note[]
     */
    public function getNotes()
    {
        return $this->_getJobItems('efi/job_note_collection');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Part[]
     */
    public function getParts()
    {
        /** @var Blackbox_Epace_Model_Epace_Job_Part[] $parts */
        $parts = $this->_getJobItems('efi/job_part_collection');
        if ($estimate = $this->getEstimate()) {
            foreach ($parts as $part) {
                if ($part->getData('estimate') == $estimate->getData('estimateNumber')) {
                    $part->setEstimate($estimate);
                }
            }
        }

        return $parts;
    }

    public function getDefinition()
    {
        return [
            'addPlanFromJobType' => '',
            'epaceEstimate' => '',
            'billPartsTogether' => '',
            'customer' => 'string',
            'description' => '',
            'description2' => '',
            'enteredBy' => '',
            'job' => 'string',
            'jobType' => 'int',
            'totalParts' => '',
            'salesPerson' => 'int',
            'csr' => 'int',
            'adminStatus' => 'string',
            'prevAdminStatus' => 'string',
            'shipVia' => 'int',
            'terms' => '',
            'allowableOvers' => '',
            'dateSetup' => 'date',
            'timeSetUp' => 'date',
            'promiseDate' => 'date',
            'scheduledShipDateForced' => '',
            'scheduledShipTimeForced' => '',
            'contactFirstName' => '',
            'contactLastName' => '',
            'priority' => '',
            'quoteNumber' => '',
            'priceList' => '',
            'oversMethod' => '',
            'shipInNameOf' => '',
            'numbersGuaranteed' => '',
            'convertingToJob' => '',
            'jobOrderType' => '',
            'comboJobPercentageCalculationType' => '',
            'freightOnBoard' => '',
            'altCurrency' => '',
            'altCurrencyRate' => '',
            'altCurrencyRateSource' => '',
            'altCurrencyRateSourceNote' => 'string',
            'createdFromAnsix12850' => '',
            'readyToSchedule' => '',
            'useLegacyPrintFlowFormatPrePress' => '',
            'useLegacyPrintFlowFormatFinishing' => '',
            'billToJobContact' => '',
            'jdfSubmitted' => '',
            'jobValue' => '',
            'destinationBasedTaxing' => '',
            'dsfCreditCardFinalized' => '',
            'invoiceW2POrderAmount' => '',
            'invoiceW2PShippingAmount' => '',
            'invoiceW2PTaxAmount' => '',
            'invoiceW2PHandlingAmount' => '',
            'manufacturingLocation' => '',
            'taxCategoryForced' => '',
            'comboDirty' => '',
            'invoiceLevelOptions' => '',
            'amountToInvoice' => '',
            'amountToInvoiceForced' => '',
            'quantityOrdered' => '',
            'quantityOrderedForced' => '',
            'originalQuotedPrice' => '',
            'originalQuotedPriceForced' => '',
            'salesCategory' => '',
            'salesCategoryForced' => '',
            'invoiceUOM' => '',
            'invoiceUOMForced' => '',
            'amountInvoiced' => '',
            'changeOrderTotal' => '',
            'freightAmountTotal' => '',
            'executeSync' => '',
            'comboTotal' => '',
            'currentStatus' => '',
            'earliestProofDue' => '',
            'earliestProofShipDateTime' => '',
            'totalPriceAllParts' => '',
            'quantityRemaining' => '',
            'qtyOrdered' => '',
            'scheduled' => '',
            'prePressScheduled' => '',
            'finishingScheduled' => '',
            'promptForMultipleParts' => '',
            'promptForMultipleProducts' => '',
            'billPartsTogetherAttribute' => '',
            'billPartOneOnlyAttribute' => '',
        ];
    }

    protected function _getJobItems($collectionName)
    {
        return $this->_getChildItems($collectionName, [
            'job' => $this->getId()
        ], function ($item) {
            $item->setJob($this);
        });
    }
}