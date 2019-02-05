<?php

class Blackbox_Epace_Model_Epace_Job extends Blackbox_Epace_Model_Epace_AbstractObject
{
    /**
     * @var Blackbox_Epace_Model_Epace_Quote
     */
    protected $quote;

    /**
     * @var Blackbox_Epace_Model_Epace_Estimate
     */
    protected $estimate;

    protected function _construct()
    {
        $this->_init('Job', 'job');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Quote
     */
    public function getQuote()
    {
        if (is_null($this->quote)) {
            $this->quote = false;
            if ($this->getData('quoteNumber')) {
                $quote = Mage::getModel('efi/estimate_quote')->load($this->getData('quoteNumber'));
                if ($quote->getId()) {
                    $this->quote = $quote;
                }
            }
        }

        return $this->quote;
    }

    public function setQuote(Blackbox_Epace_Model_Epace_Quote $quote)
    {
        $this->quote = $quote;

        return $this;
    }

    public function isSourceEstimate()
    {
        return $this->getData('altCurrencyRateSource') == 'Estimate';
    }

    public function getEstimate()
    {
        if (is_null($this->estimate)) {
            $this->estimate = false;

            if ($this->isSourceEstimate()) {
                $estimate = Mage::getModel('efi/estimate')->load($this->getData('altCurrencyRateSourceNote'));
                if ($estimate->getId()) {
                    $this->estimate = $estimate;
                }
            }
        }

        return $this->estimate;
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
        return $this->_getJobItems('efi/job_part_collection');
    }

    public function getDefinition()
    {
        return [
            'addPlanFromJobType' => '',
            'epaceEstimate' => '',
            'billPartsTogether' => '',
            'customer' => '',
            'description' => '',
            'description2' => '',
            'enteredBy' => '',
            'job' => 'string',
            'jobType' => '',
            'totalParts' => '',
            'salesPerson' => '',
            'csr' => '',
            'adminStatus' => '',
            'prevAdminStatus' => '',
            'shipVia' => '',
            'terms' => '',
            'allowableOvers' => '',
            'dateSetup' => '',
            'timeSetUp' => '',
            'promiseDate' => '',
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
            'altCurrencyRateSourceNote' => '',
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
        /** @var Blackbox_Epace_Model_Resource_Epace_Collection $collection */
        $collection = Mage::getResourceModel($collectionName);
        /** @var Blackbox_Epace_Model_Epace_Job_AbstractChild[] $items */
        $items = $collection->addFilter('job', $this->getId())->getItems();
        foreach ($items as $item) {
            $item->setJob($this);
        }

        return $items;
    }
}