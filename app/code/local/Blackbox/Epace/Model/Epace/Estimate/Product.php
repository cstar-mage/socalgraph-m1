<?php

/**
 * @method string getDescription()
 *
 * Class Blackbox_Epace_Model_Epace_Estimate_Product
 */
class Blackbox_Epace_Model_Epace_Estimate_Product extends Blackbox_Epace_Model_Epace_Estimate_AbstractChild
{
    protected function _construct()
    {
        $this->_init('EstimateProduct', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate_Part[]
     */
    public function getParts()
    {
        return $this->_getChildItems('efi/estimate_part_collection', [
            'estimateProduct' => (int)$this->getId()
        ], function ($part) {
            $part->setEstimate($this->getEstimate())->setProduct($this);
        });
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate_Product_PriceSummary[]
     */
    public function getPriceSummaries()
    {
        return $this->_getChildItems('efi/estimate_product_priceSummary_collection', [
            'estimateProduct' => (int)$this->getId()
        ], function ($priceSummary) {
            $priceSummary->setProduct($this);
        });
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'sequence' => '',
            'estimate' => '',
            'description' => '',
            'systemGenerated' => 'bool',
            'singleWebDelivery' => 'bool',
            'manufacturingLocation' => '',
            'lookupUrl' => '',
            'wrapRearWindow' => 'bool',
            'wrapSideWindow' => 'bool',
            'secondSurface' => 'bool'
        ];
    }
}