<?php

class Blackbox_Epace_Model_Epace_Estimate_Product_PriceSummary extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('EstimateProductPriceSummary', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate_Product|false
     */
    public function getProduct()
    {
        return $this->_getObject('product', 'estimateProduct', 'efi/estimate_product');
    }

    public function setProduct(Blackbox_Epace_Model_Epace_Estimate_Product $product)
    {
        return $this->_setObject('product', $product);
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'estimateProduct' => '',
            'quantityNum' => '',
            'quotedPricePerAddlM' => '',
            'quotedPricePerAddlMForced' => '',
            'quantityOrdered' => '',
            'multipleQuantities' => '',
            'quotedPrice' => '',
            'quotedPriceForced' => '',
            'price' => '',
            'valueAddedPrice' => '',
            'nonValueAddedPrice' => '',
            'taxBase' => '',
            'taxAmount' => '',
            'pricePerAddlM' => '',
            'quotedPricePerAddl100' => '',
            'quotedPricePerAddl100Forced' => '',
            'pricePerAddl100' => '',
            'grandTotal' => '',
            'pricingUOM' => '',
            'pricePerUOM' => '',
            'pricePerUOMForced' => '',
            'pricePerAddlUOM' => '',
            'pricePerAddlUOMForced' => '',
            'pricingUnits' => '',
            'pricingUnitsForced' => '',
            'overallMarkupForced' => '',
            'valueAddedMarkupForced' => '',
            'nonValueAddedMarkupForced' => '',
            'paperMarkup' => '',
            'paperMarkupForced' => '',
            'outsidePurchaseMarkupForced' => '',
            'outsidePurchaseSetupMarkupForced' => '',
            'maxPaymentTermDiscount' => '',
            'overallSellMarkup' => '',
            'overallSellMarkupForced' => '',
            'priceLevel' => '',
            'effectiveCommissionRateForced' => '',
            'quotedPricePerM' => '',
            'quotedPricePer100' => '',
            'quotedPricePerEach' => '',
            'quotedPricePerAddlEach' => '',
            'quantity' => '',
        ];
    }
}