<?php

/**
 * @method string getDescription()
 * @method float getEstimatedCost()
 * @method float getQtyOrdered()
 * @method float getValue()
 * @method float getQuotedPrice()
 * @method float getTargetSellPrice()
 *
 * Class Blackbox_Epace_Model_Epace_Job_Part
 */
class Blackbox_Epace_Model_Epace_Job_Part extends Blackbox_Epace_Model_Epace_Job_AbstractChild
{
    /**
     * @var Blackbox_Epace_Model_Epace_Job_Product
     */
    protected $product = null;

    /**
     * @var Blackbox_Epace_Model_Epace_Estimate
     */
    protected $estimate = null;

    /**
     * @var Blackbox_Epace_Model_Epace_Estimate_Part
     */
    protected $estimatePart = null;

    protected function _construct()
    {
        $this->_init('JobPart', 'primaryKey');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Product
     */
    public function getProduct()
    {
        if (is_null($this->product)) {
            $product = Mage::getModel('efi/job_product')->load($this->getData('jobProduct'));
            if ($product->getId()) {
                $this->product = $product;
            } else {
                $this->product = false;
            }
        }

        return $this->product;
    }

    public function setProduct(Blackbox_Epace_Model_Epace_Job_Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate|bool
     */
    public function getEstimate()
    {
        if (is_null($this->estimate)) {
            $this->estimate = false;
            /** @var Blackbox_Epace_Model_Resource_Epace_Estimate_Collection $collection */
            $collection = Mage::getResourceModel('efi/estimate_collection');
            $collection->addFilter('estimateNumber', $this->getData('estimate'));
            $collection->setPageSize(1)->setCurPage(1);
            $estimate = $collection->getFirstItem();
            if ($estimate && $estimate->getId()) {
                $this->estimate = $estimate;
            }
        }
        return $this->estimate;
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Estimate $estimate
     * @return $this
     */
    public function setEstimate(Blackbox_Epace_Model_Epace_Estimate $estimate)
    {
        $this->estimate = $estimate;

        return $this;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate_Part|false
     */
    public function getEstimatePart()
    {
        if (is_null($this->estimatePart)) {
            $this->estimatePart = false;

            if (!empty($this->getData('estimatePart')) && $this->getEstimate()) {
                $part = $this->getEstimate()->getParts()[(int)$this->getData('estimatePart')];
                if ($part) {
                    $this->estimatePart = $part;
                }
            }
        }

        return $this->estimatePart;
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Estimate_Part $estimatePart
     * @return $this
     */
    public function setEstimatePart(Blackbox_Epace_Model_Epace_Estimate_Part $estimatePart)
    {
        $this->estimatePart = $estimatePart;

        return $this;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Material[]
     */
    public function getMaterials()
    {
        return $this->_getPartItems('efi/job_material_collection');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Part_PrePressOp[]
     */
    public function getPrePressOps()
    {
        return $this->_getPartItems('efi/job_part_prePressOp_collection');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Part_PressForm[]
     */
    public function getPressForms()
    {
        return $this->_getPartItems('efi/job_part_pressForm_collection');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Part_FinishingOp[]
     */
    public function getFinishingOps()
    {
        return $this->_getPartItems('efi/job_part_finishingOp_collection');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Part_OutsidePurch[]
     */
    public function getOutsidePurchs()
    {
        return $this->_getPartItems('efi/job_part_outsidePurch_collection');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Plan[]
     */
    public function getPlans()
    {
        return $this->_getPartItems('efi/job_plan_collection', [
            'job' => $this->getData('job'),
            'part' => $this->getData('jobPart')
        ]);
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Material[]
     */
    public function getCosts()
    {
        return $this->_getPartItems('efi/job_material_collection');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Part_SizeAllowance[]
     */
    public function getSizeAllowances()
    {
        return $this->_getPartItems('efi/job_part_sizeAllowance_collection');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Invoice[]
     */
    public function getInvoices()
    {
        return $this->_getPartItems('efi/invoice_collection', [
            'job' => $this->getData('job'),
            'jobPart' => $this->getData('jobPart')
        ]);
    }

    public function getDefinition()
    {
        return [
            'visualOpeningSizeHeight' => '',
            'visualOpeningSizeWidth' => '',
            'allowancesChanged' => 'bool',
            'metrixEnabled' => 'bool',
            'gripperColorBar' => '',
            'metrixID' => '',
            'metrixComponentID' => '',
            'billRate' => '',
            'billUOM' => '',
            'binderyMethod' => '',
            'binderyWork' => '',
            'bleedsAcross' => '',
            'bleedsAlong' => '',
            'colorsS1' => '',
            'colorsS2' => '',
            'colorsTotal' => '',
            'dateSetup' => '',
            'description' => '',
            'desktop' => '',
            'estCostPerM' => '',
            'estimate' => '',
            'estimatePart' => '',
            'estimatedCost' => '',
            'estimator' => '',
            'flatSizeH' => '',
            'flatSizeW' => '',
            'finalSizeH' => '',
            'finalSizeW' => '',
            'trimSizeHeight' => '',
            'trimSizeWidth' => '',
            'folderNumUp' => '',
            'foldPattern' => '',
            'foldPatternDesc' => '',
            'grainSpecifications' => '',
            'inkDescS1' => '',
            'invoiceSequence' => '',
            'job' => '',
            'jobCost01' => '',
            'jobPart' => '',
            'jobType' => '',
            'lastActCode' => '',
            'lastActDate' => '',
            'lastActTime' => '',
            'materialProvided' => '',
            'numPrsShtsOut' => '',
            'numSigs' => '',
            'loadBalanced' => 'bool',
            'pages' => '',
            'parallelFolds' => '',
            'plates' => '',
            'prep' => '',
            'separateLayout' => 'bool',
            'press1' => '',
            'pressSheetNumOut' => '',
            'jobProductType' => '',
            'priority' => '',
            'productionStatus' => '',
            'proofs' => '',
            'qtyOrdered' => '',
            'qtyToMfg' => '',
            'quotedPrice' => '',
            'quotedPriceForced' => 'bool',
            'quotePerM' => '',
            'rightFolds' => '',
            'runMethod' => '',
            'salesCategory' => '',
            'sheetsNetRequired' => '',
            'sheetsOffPress' => '',
            'sheetsToPress' => '',
            'stitcherNumUp' => '',
            'timeSetUp' => '',
            'totalHours' => '',
            'useBasicJacket' => 'bool',
            'numPressForms' => '',
            'numSigsOddPressForm' => '',
            'numPlies' => '',
            'calculating' => 'bool',
            'manufacturingLocation' => '',
            'originalManufacturingLocation' => '',
            'jogTrim' => '',
            'nonImageHead' => '',
            'nonImageFoot' => '',
            'nonImageSpine' => '',
            'nonImageFace' => '',
            'run' => '',
            'paceConnectFileType' => '',
            'shippingWorkflow' => '',
            'prepressWorkflow' => '',
            'useLegacyPrintFlowFormat' => 'bool',
            'directMailPart' => 'bool',
            'bindingSide' => '',
            'jogSide' => '',
            'jdfSubmitted' => 'bool',
            'lastStatusChangedDate' => '',
            'lastStatusChangedTime' => '',
            'proofRequired' => 'bool',
            'proofPart' => 'bool',
            'resolution' => '',
            'tileProduct' => 'bool',
            'seamDirection' => '',
            'usePressForms' => 'bool',
            'gangable' => 'bool',
            'jobProduct' => '',
            'finishedAutoImport' => 'bool',
            'value' => '',
            'productionType' => '',
            'queueDestination' => '',
            'requiresImposition' => 'bool',
            'componentDescription' => '',
            'estimateVersion' => '',
            'invoiceW2POrderAmount' => 'bool',
            'invoiceW2PShippingAmount' => 'bool',
            'invoiceW2PTaxAmount' => 'bool',
            'invoiceW2PHandlingAmount' => 'bool',
            'printRunMethod' => '',
            'mxmlLayoutInvalid' => 'bool',
            'originalQuotedPrice' => '',
            'originalQuotedPriceForced' => 'bool',
            'originalQuotedPricePerM' => '',
            'originalQuotedPricePerMForced' => 'bool',
            'transactionHours' => '',
            'transactionCosts' => '',
            'colors' => '',
            'totalCost' => '',
            'targetSellPrice' => '',
            'quantityRemaining' => '',
            'scheduled' => 'bool',
            'includeMailing' => 'bool',
            'calculatedTabSpine' => '',
            'calculatedTabFace' => '',
            'calculatedTabHead' => '',
            'calculatedTabFoot' => '',
            'calculatedBleedsSpine' => '',
            'calculatedBleedsFace' => '',
            'calculatedBleedsHead' => '',
            'calculatedBleedsFoot' => '',
            'calculatedTrimSpine' => '',
            'calculatedTrimFace' => '',
            'calculatedTrimHead' => '',
            'calculatedTrimFoot' => '',
            'calculatedOddPanelSpineSize' => '',
            'calculatedNumOddPanelsSpine' => '',
            'calculatedOddPanelWidthSize' => '',
            'calculatedNumOddPanelsWidth' => '',
            'calculatedSpineSize' => '',
            'primaryKey' => '',
            'foldPatternKey' => '',
        ];
    }

    /**
     * @param $collectionName
     * @return Blackbox_Epace_Model_Epace_Job_Part_AbstractChild[]
     */
    protected function _getPartItems($collectionName, $filters = null)
    {
        if (!$filters) {
            $filters = [
                'jobPart' => $this->getId()
            ];
        }
        /** @var Blackbox_Epace_Model_Resource_Epace_Collection $collection */
        $collection = Mage::getResourceModel($collectionName);
        foreach ($filters as $field => $value) {
            $collection->addFilter($field, $value);
        }
        /** @var Blackbox_Epace_Model_Epace_Job_Part_AbstractChild[] $items */
        $items = $collection->getItems();
        $job = $this->getJob();
        foreach ($items as $item) {
            if ($job) {
                $item->setJob($job);
            }
            $item->setPart($this);
        }

        return $items;
    }
}