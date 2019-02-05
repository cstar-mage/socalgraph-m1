<?php

class Blackbox_Epace_Model_Epace_Estimate_QuoteLetter extends Blackbox_Epace_Model_Epace_Estimate_AbstractChild
{
    protected function _construct()
    {
        $this->_init('EstimateQuoteLetter', 'id');
    }

    public function getNotes()
    {
        /** @var Blackbox_Epace_Model_Resource_Epace_Estimate_QuoteLetter_Note_Collection $collection */
        $collection = Mage::getResourceModel('efi/estimate_quoteLetter_note_collection');
        $items = $collection->addFilter('estimateQuoteLetter', $this->getId())->getItems();
        foreach ($items as $item) {
            $item->setQuoteLetter($this);
        }
        return $this;
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'printPriceGrid' => 'bool',
            'quoteLetterType' => '',
            'estimate' => '',
            'date' => '',
            'salutation' => '',
            'body' => '',
            'comment' => '',
            'closing' => '',
            'accepted' => 'bool',
            'priceDetailLevel' => '',
        ];
    }
}