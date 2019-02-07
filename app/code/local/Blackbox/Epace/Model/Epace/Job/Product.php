<?php

/**
 * @method string getDescription()
 * @method int getQtyOrdered()
 * @method float getProductValue()
 * @method float getAmountToInvoice()
 * @method float getOriginalQuotedPrice()
 * @method float getAmountInvoiced()
 * @method int getQtyRemaining()
 *
 * Class Blackbox_Epace_Model_Epace_Job_Product
 */
class Blackbox_Epace_Model_Epace_Job_Product extends Blackbox_Epace_Model_Epace_Job_AbstractChild
{
    protected function _construct()
    {
        $this->_init('JobProduct', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Part[]
     */
    public function getParts()
    {
        /** @var Blackbox_Epace_Model_Resource_Epace_Job_Part_Collection $collection */
        $collection = Mage::getResourceModel('efi/job_part_collection');
        $parts = $collection->addFilter('jobProduct', (int)$this->getId())->getItems();
        $job = $this->getJob();
        foreach ($parts as $part) {
            if ($job) {
                $part->setJob($job);
            }
            $part->setProduct($this);
        }

        return $parts;
    }

    public function getDefinition()
    {
        return [
            'sequence' => '',
            'id' => 'int',
            'proof' => '',
            'job' => '',
            'description' => '',
            'qtyOrdered' => '',
            'qtyOrderedForced' => '',
            'jdfSubmitted' => '',
            'singleWebDelivery' => '',
            'productValue' => '',
            'manufacturingLocation' => '',
            'taxCategoryForced' => '',
            'lookupURL' => '',
            'wrapRearWindow' => '',
            'wrapSideWindow' => '',
            'secondSurface' => '',
            'estimateSource' => '',
            'amountToInvoice' => '',
            'amountToInvoiceForced' => '',
            'salesCategory' => '',
            'salesCategoryForced' => '',
            'originalQuotedPrice' => '',
            'originalQuotedPriceForced' => '',
            'invoiceUOM' => '',
            'invoiceUOMForced' => '',
            'qtyRemaining' => '',
        ];
    }
}