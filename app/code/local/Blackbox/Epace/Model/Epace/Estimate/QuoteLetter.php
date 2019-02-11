<?php

class Blackbox_Epace_Model_Epace_Estimate_QuoteLetter extends Blackbox_Epace_Model_Epace_Estimate_AbstractChild
{
    protected function _construct()
    {
        $this->_init('EstimateQuoteLetter', 'id');
    }

    public function getNotes()
    {
        return $this->_getChildItems('efi/estimate_quoteLetter_note_collection', [
            'estimateQuoteLetter' => $this->getId()
        ], function ($item) {
            $item->setQuoteLetter($this);
        });
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