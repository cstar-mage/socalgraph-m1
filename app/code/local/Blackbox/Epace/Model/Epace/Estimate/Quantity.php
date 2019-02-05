<?php

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