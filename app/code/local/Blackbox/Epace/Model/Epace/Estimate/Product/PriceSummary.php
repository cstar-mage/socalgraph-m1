<?php

class Blackbox_Epace_Model_Epace_Estimate_Product_PriceSummary extends Blackbox_Epace_Model_Epace_AbstractObject
{
    /**
     * @var Blackbox_Epace_Model_Epace_Estimate_Product
     */
    protected $product = null;

    protected function _construct()
    {
        $this->_init('EstimateProductPriceSummary', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate_Product|false
     */
    public function getProduct()
    {
        if (is_null($this->product)) {
            $product = Mage::getModel('efi/estimate_product')->load($this->getData('estimateProduct'));
            if ($product->getId()) {
                $this->product = $product;
            } else {
                $this->product = false;
            }
        }

        return $this->product;
    }

    public function setProduct(Blackbox_Epace_Model_Epace_Estimate_Product $product)
    {
        $this->product = $product;

        return $this;
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