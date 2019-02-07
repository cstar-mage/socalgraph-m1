<?php

/**
 * @method int getQuantityOrdered()
 * @method float getPrice()
 * @method float getTaxBase()
 * @method float getTaxAmount()
 * @method float getCost()
 * @method float getTargetSell()
 * @method float getMarkup()
 * @method float getMarkupPercent()
 * @method float getGrandTotal()
 * @method float getTaxEffectivePercent()
 * @method bool getOverallMarkupForced()
 * @method float getValueAddedPrice()
 * @method float getNonValueAddedPrice()
 * @method bool getValueAddedMarkupForced()
 * @method bool getNonValueAddedMarkupForced()
 * @method float getOverallSellMarkup()
 * @method int getGripperColorBar()
 * @method int getSheetsOffPress()
 * @method int getNumSigsPerPressForm()
 * @method int getNumSigsOddPressForm()
 * @method int getPosition()
 * @method float getQuotedPrice()
 * @method bool getQuotedPriceForced()
 * @method float getQuotedPricePerAddlM()
 * @method bool getQuotedPricePerAddlMForced()
 * @method float getValueAdded()
 * @method float getNonValueAdded()
 * @method float getValueAddedPercent()
 * @method float getNonValueAddedPercent()
 * @method float getWeightPerPiece()
 * @method float getComboPercent()
 * @method bool getComboPercentForced()
 * @method float getPricePerEach()
 * @method bool getPricePerEachForced()
 * @method float getQuotedPricePerAddl100()
 * @method bool getQuotedPricePerAddl100Forced()
 * @method float getPricePerAddl100()
 * @method bool getPricePerAddl100Forced()
 * @method bool getNewQuantity()
 * @method bool getDirty()
 * @method float getPaperMarkup()
 * @method bool getPaperMarkupForced()
 * @method float getOutsidePurchaseMarkup()
 * @method bool getOutsidePurchaseMarkupForced()
 * @method float getOutsidePurchaseSetupMarkup()
 * @method bool getOutsidePurchaseSetupMarkupForced()
 * @method bool getAlternatePrintMethodApplied()
 * @method float getContributionAnalysisTaxAmount()
 * @method float getPricePerUOM()
 * @method bool getPricePerUOMForced()
 * @method float getPricePerAddlUOM()
 * @method bool getPricePerAddlUOMForced()
 * @method float getPricingUnits()
 * @method bool getPricingUnitsForced()
 * @method bool getEffectiveCommissionRateForced()
 * @method string getChartDescription()
 * @method float getValueAddedPerPressHour()
 * @method float getOverallInkCoverageSide1()
 * @method float getOverallInkCoverageSide2()
 * @method float getCommRate()
 * @method float getAdditionalWeightPerPiece()
 *
 * Class Blackbox_Epace_Model_Epace_Estimate_Quantity
 */
class Blackbox_Epace_Model_Epace_Estimate_Quantity extends Blackbox_Epace_Model_Epace_Estimate_AbstractChild
{
    use Blackbox_Epace_Model_Epace_Estimate_Part_ChildTrait;

    protected function _construct()
    {
        $this->_init('EstimateQuantity', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'estimatePart' => '',
            'quantityOrdered' => '',
            'metrixID' => '',
            'price' => '',
            'taxBase' => '',
            'taxAmount' => '',
            'pricePerAddlM' => '',
            'cost' => '',
            'targetSell' => '',
            'markup' => '',
            'markupPercent' => '',
            'grandTotal' => '',
            'overallMarkupForced' => '',
            'valueAddedPrice' => '',
            'nonValueAddedPrice' => '',
            'valueAddedMarkupForced' => '',
            'nonValueAddedMarkupForced' => '',
            'overallSellMarkup' => '',
            'overallSellMarkupForced' => '',
            'gripperColorBar' => '',
            'sheetsOffPress' => '',
            'numSigsPerPressForm' => '',
            'numSigsOddPressForm' => '',
            'position' => '',
            'quotedPrice' => '',
            'quotedPriceForced' => '',
            'quotedPricePerAddlM' => '',
            'quotedPricePerAddlMForced' => '',
            'valueAdded' => '',
            'nonValueAdded' => '',
            'valueAddedPercent' => '',
            'nonValueAddedPercent' => '',
            'weightPerPiece' => '',
            'comboPercent' => '',
            'comboPercentForced' => '',
            'pricePerEach' => '',
            'pricePerEachForced' => '',
            'quotedPricePerAddl100' => '',
            'quotedPricePerAddl100Forced' => '',
            'pricePerAddl100' => '',
            'pricePerAddl100Forced' => '',
            'newQuantity' => '',
            'dirty' => '',
            'paperMarkup' => '',
            'paperMarkupForced' => '',
            'outsidePurchaseMarkupForced' => '',
            'outsidePurchaseSetupMarkupForced' => '',
            'alternatePrintMethodApplied' => '',
            'contributionAnalysisTaxAmount' => '',
            'pricePerUOM' => '',
            'pricePerUOMForced' => '',
            'pricePerAddlUOM' => '',
            'pricePerAddlUOMForced' => '',
            'pricingUnits' => '',
            'pricingUnitsForced' => '',
            'effectiveCommissionRateForced' => '',
            'estimate' => '',
            'chartDescription' => '',
            'valueAddedPerPressHour' => '',
            'overallInkCoverageSide1' => '',
            'overallInkCoverageSide2' => '',
            'commRate' => '',
            'additionalWeightPerPiece' => '',
        ];
    }
}