<?php

/**
 * @method string getDescription()
 *
 * Class Blackbox_Epace_Model_Epace_Estimate_Part
 */
class Blackbox_Epace_Model_Epace_Estimate_Part extends Blackbox_Epace_Model_Epace_Estimate_AbstractChild
{
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
        return $this->_setObject('product', $product);
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
            'metrixEnabled' => 'bool',
            'includeMailing' => 'bool',
            'estimate' => 'int',
            'numSigs' => 'int',
            'description' => 'string',
            'jobProductType' => 'string',
            'productionType' => 'int',
            'numPages' => 'int',
            'foldPattern' => 'int',
            'bindingSide' => 'string',
            'jogSide' => 'string',
            'numPlies' => 'int',
            'isOneWebOnly' => 'bool',
            'separateLayout' => 'bool',
            'prepressWorkflow' => 'float',
            'finalSizeHeight' => 'float',
            'finalSizeWidth' => 'float',
            'flatSizeHeight' => 'float',
            'flatSizeWidth' => 'float',
            'trimSizeHeight' => 'float',
            'trimSizeWidth' => 'float',
            'bindingMethod' => 'int',
            'shippingWorkflow' => 'int',
            'registerSide1' => 'int',
            'registerSide2' => 'int',
            'colorsSide1' => 'int',
            'colorsSide2' => 'int',
            'colorsTotal' => 'int',
            'grainSpecifications' => 'int',
            'bleedsAlong' => 'int',
            'bleedsAcross' => 'int',
            'chargeableMakereadyPercent' => 'float',
            'difficulty' => 'int',
            'outsidePurchaseMarkup' => 'float',
            'outsidePurchaseMarkupForced' => 'bool',
            'coating' => 'int',
            'coatingDry' => 'bool',
            'coatingSides' => 'int',
            'varnishDry' => 'bool',
            'inkType' => 'int',
            'priceLevel' => 'int',
            'priceLevelForced' => 'bool',
            'secondWeb' => 'bool',
            'gangable' => 'bool',
            'jogTrim' => 'float',
            'salesCategory' => 'int',
            'inkCoverageFront' => 'int',
            'pressInkType' => 'int',
            'speedFactor' => 'int',
            'itemDiscountPercentForced' => 'bool',
            'eachOfPricing' => 'bool',
            'prepressWorkflowChanged' => 'bool',
            'productTypeChanged' => 'bool',
            'bindingMethodChanged' => 'bool',
            'shippingWorkflowChanged' => 'bool',
            'pressEventWorkflowChanged' => 'bool',
            'outsidePurchaseWorkflowChanged' => 'bool',
            'inkChanged' => 'bool',
            'varnishChanged' => 'bool',
            'coatingChanged' => 'bool',
            'primaryPressChanged' => 'bool',
            'tileProduct' => 'bool',
            'seamDirection' => 'string',
            'estimateProduct' => 'int',
            'requiresImposition' => 'bool',
            'componentDescription' => 'string',
            'manufacturingLocation' => 'int',
            'sequence' => 'float',
            'lastQuantityConverted' => 'int',
            'fromComposite' => 'bool',
            'totalPages' => 'int',
            'tabSpine' => 'float',
            'tabFace' => 'float',
            'tabHead' => 'float',
            'tabFoot' => 'float',
            'bleedsSpine' => 'float',
            'bleedsFace' => 'float',
            'bleedsHead' => 'float',
            'bleedsFoot' => 'float',
            'trimSpine' => 'float',
            'trimFace' => 'float',
            'trimHead' => 'float',
            'trimFoot' => 'float',
            'oddPanelSpineSize' => 'float',
            'numOddPanelsSpine' => 'int',
            'oddPanelWidthSize' => 'float',
            'numOddPanelsWidth' => 'int',
            'spineSize' => 'float',
            'visualOpeningSizeHeight' => 'float',
            'visualOpeningSizeWidth' => 'float',
            'foldPatternKey' => 'string',
        ];
    }
}