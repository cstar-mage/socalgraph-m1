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
    public function getEstimateProduct()
    {
        return $this->_getObject('estimateProduct', 'estimateProduct', 'efi/estimate_product');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Estimate_Product $product
     * @return $this
     */
    public function setEstimateProduct(Blackbox_Epace_Model_Epace_Estimate_Product $product)
    {
        return $this->_setObject('estimateProduct', $product);
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate_Product|false
     * @deprecated
     */
    public function getProduct()
    {
        return $this->getEstimateProduct();
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Estimate_Product $product
     * @return $this
     * @deprecated
     */
    public function setProduct(Blackbox_Epace_Model_Epace_Estimate_Product $product)
    {
        return $this->setEstimateProduct($product);
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'estimateProduct' => 'int',
            'quantityNum' => 'int',
            'quotedPricePerAddlM' => 'float',
            'quotedPricePerAddlMForced' => 'bool',
            'quantityOrdered' => 'int',
            'multipleQuantities' => 'bool',
            'quotedPrice' => 'float',
            'quotedPriceForced' => 'bool',
            'price' => 'float',
            'valueAddedPrice' => 'float',
            'nonValueAddedPrice' => 'float',
            'taxBase' => 'float',
            'taxAmount' => 'float',
            'pricePerAddlM' => 'float',
            'quotedPricePerAddl100' => 'float',
            'quotedPricePerAddl100Forced' => 'bool',
            'pricePerAddl100' => 'float',
            'grandTotal' => 'float',
            'pricingUOM' => 'string',
            'pricePerUOM' => 'float',
            'pricePerUOMForced' => 'bool',
            'pricePerAddlUOM' => 'float',
            'pricePerAddlUOMForced' => 'bool',
            'pricingUnits' => 'float',
            'pricingUnitsForced' => 'bool',
            'overallMarkupForced' => 'bool',
            'valueAddedMarkupForced' => 'bool',
            'nonValueAddedMarkupForced' => 'bool',
            'paperMarkup' => 'float',
            'paperMarkupForced' => 'bool',
            'outsidePurchaseMarkupForced' => 'bool',
            'outsidePurchaseSetupMarkupForced' => 'bool',
            'maxPaymentTermDiscount' => 'float',
            'overallSellMarkup' => 'float',
            'overallSellMarkupForced' => 'bool',
            'priceLevel' => 'int',
            'effectiveCommissionRateForced' => 'bool',
            'quotedPricePerM' => 'float',
            'quotedPricePer100' => 'float',
            'quotedPricePerEach' => 'float',
            'quotedPricePerAddlEach' => 'float',
            'quantity' => 'string',
        ];
    }
}