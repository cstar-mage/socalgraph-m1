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
        return $this->_getChildItems('efi/job_part_collection', [
            'jobProduct' => (int)$this->getId()
        ], function ($part) {
            if ($this->getJob()) {
                $part->setJob($this->getJob());
            }
            $part->setProduct($this);
        });
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