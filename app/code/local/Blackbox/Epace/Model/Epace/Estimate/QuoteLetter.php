<?php

/**
 * @method bool getPrintPriceGrid()
 * @method int getQuoteLetterType()
 * @method string getDate()
 * @method string getSalutation()
 * @method string getBody()
 * @method string getComment()
 * @method string getClosing()
 * @method string getInternalNote()
 * @method bool getAccepted()
 * @method getPriceDetailLevel()
 *
 * Class Blackbox_Epace_Model_Epace_Estimate_QuoteLetter
 */
class Blackbox_Epace_Model_Epace_Estimate_QuoteLetter extends Blackbox_Epace_Model_Epace_Estimate_AbstractChild
{
    protected function _construct()
    {
        $this->_init('EstimateQuoteLetter', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate_QuoteLetter_Note[]
     */
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
            'quoteLetterType' => 'int',
            'estimate' => 'int',
            'date' => 'date',
            'salutation' => 'string',
            'body' => 'string',
            'comment' => 'string',
            'closing' => 'string',
            'internalNote' => 'string',
            'accepted' => 'bool',
            'priceDetailLevel' => '',
        ];
    }
}