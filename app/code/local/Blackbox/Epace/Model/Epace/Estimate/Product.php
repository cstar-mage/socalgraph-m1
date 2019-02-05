<?php

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
        /** @var Blackbox_Epace_Model_Resource_Epace_Estimate_Part_Collection $collection */
        $collection = Mage::getResourceModel('efi/estimate_part_collection');
        $parts = $collection->addFilter('estimateProduct', (int)$this->getId())->getItems();
        foreach ($parts as $part) {
            $part->setEstimate($this->getEstimate())->setProduct($this);
        }

        return $parts;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate_Product_PriceSummary[]
     */
    public function getPriceSummaries()
    {
        /** @var Blackbox_Epace_Model_Resource_Epace_Estimate_Product_PriceSummary_Collection $collection */
        $collection = Mage::getResourceModel('efi/estimate_product_priceSummary_collection');
        $priceSummaries = $collection->addFilter('estimateProduct ', (int)$this->getId())->getItems();
        foreach ($priceSummaries as $priceSummary) {
            $priceSummary->setProduct($this);
        }

        return $priceSummaries;
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