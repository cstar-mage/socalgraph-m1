<?php

/**
 * @method string getDescription()
 *
 * Class Blackbox_Epace_Model_Epace_Estimate_Part
 */
class Blackbox_Epace_Model_Epace_Estimate_Part extends Blackbox_Epace_Model_Epace_Estimate_AbstractChild
{
    /**
     * @var Blackbox_Epace_Model_Epace_Estimate_Product
     */
    protected $product = null;

    protected function _construct()
    {
        $this->_init('EstimatePart', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate_Product
     */
    public function getProduct()
    {
        return $this->_getObject('product', 'estimateProduct', 'efi/estimate_product');
    }

    public function setProduct(Blackbox_Epace_Model_Epace_Estimate_Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate_Quantity[]
     */
    public function getQuantities()
    {
        return $this->_getChildItems('efi/estimate_quantity_collection', [
            'estimatePart' => (int)$this->getId()
        ], function ($quantity) {
            $quantity->setEstimate($this->getEstimate())->setPart($this);
        });
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate_Part_SizeAllowance[]
     */
    public function getSizeAllowances()
    {
        return $this->_getChildItems('efi/estimate_part_sizeAllowance_collection', [
            'estimatePart' => (int)$this->getId()
        ], function ($sizeAllowance) {
            $sizeAllowance->setPart($this);
        });
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'metrixEnabled' => '',
            'includeMailing' => '',
            'estimate' => '',
            'numSigs' => '',
            'description' => '',
            'jobProductType' => '',
            'productionType' => '',
            'numPages' => '',
            'foldPattern' => '',
            'bindingSide' => '',
            'jogSide' => '',
            'numPlies' => '',
            'isOneWebOnly' => '',
            'separateLayout' => '',
            'prepressWorkflow' => '',
            'finalSizeHeight' => '',
            'finalSizeWidth' => '',
            'flatSizeHeight' => '',
            'flatSizeWidth' => '',
            'bindingMethod' => '',
            'shippingWorkflow' => '',
            'registerSide1' => '',
            'registerSide2' => '',
            'colorsSide1' => '',
            'colorsSide2' => '',
            'colorsTotal' => '',
            'grainSpecifications' => '',
            'bleedsAlong' => '',
            'bleedsAcross' => '',
            'chargeableMakereadyPercent' => '',
            'difficulty' => '',
            'outsidePurchaseMarkup' => '',
            'outsidePurchaseMarkupForced' => '',
            'coatingDry' => '',
            'varnishDry' => '',
            'inkType' => '',
            'priceLevel' => '',
            'priceLevelForced' => '',
            'secondWeb' => '',
            'gangable' => '',
            'jogTrim' => '',
            'salesCategory' => '',
            'inkCoverageFront' => '',
            'pressInkType' => '',
            'speedFactor' => '',
            'itemDiscountPercentForced' => '',
            'eachOfPricing' => '',
            'prepressWorkflowChanged' => '',
            'productTypeChanged' => '',
            'bindingMethodChanged' => '',
            'shippingWorkflowChanged' => '',
            'pressEventWorkflowChanged' => '',
            'outsidePurchaseWorkflowChanged' => '',
            'inkChanged' => '',
            'varnishChanged' => '',
            'coatingChanged' => '',
            'primaryPressChanged' => '',
            'tileProduct' => '',
            'seamDirection' => '',
            'estimateProduct' => '',
            'requiresImposition' => '',
            'componentDescription' => '',
            'manufacturingLocation' => '',
            'sequence' => '',
            'fromComposite' => '',
            'totalPages' => '',
            'tabSpine' => '',
            'tabFace' => '',
            'tabHead' => '',
            'tabFoot' => '',
            'bleedsSpine' => '',
            'bleedsFace' => '',
            'bleedsHead' => '',
            'bleedsFoot' => '',
            'trimSpine' => '',
            'trimFace' => '',
            'trimHead' => '',
            'trimFoot' => '',
            'oddPanelSpineSize' => '',
            'numOddPanelsSpine' => '',
            'oddPanelWidthSize' => '',
            'numOddPanelsWidth' => '',
            'spineSize' => '',
            'visualOpeningSizeHeight' => '',
            'visualOpeningSizeWidth' => '',
            'foldPatternKey' => '',
        ];
    }
}