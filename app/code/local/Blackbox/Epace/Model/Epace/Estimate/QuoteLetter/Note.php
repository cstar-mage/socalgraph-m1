<?php

class Blackbox_Epace_Model_Epace_Estimate_QuoteLetter_Note extends Blackbox_Epace_Model_Epace_AbstractObject
{
    /** @var Blackbox_Epace_Model_Epace_Estimate_QuoteLetter */
    protected $quoteLetter = null;

    protected function _construct()
    {
        $this->_init('EstimateQuoteLetterNote', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate_QuoteLetter|bool
     */
    public function getQuoteLetter()
    {
        return $this->_getObject('quoteLetter', 'estimateQuoteLetter', 'efi/estimate_quoteLetter');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Estimate_QuoteLetter $quoteLetter
     * @return $this
     */
    public function setQuoteLetter(Blackbox_Epace_Model_Epace_Estimate_QuoteLetter $quoteLetter)
    {
        $this->quoteLetter = $quoteLetter;

        return $this;
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'estimateQuoteLetter' => '',
            'note' => 'string',
            'printOnReport' => 'bool',
            'section' => 'string',
            'category' => 'string',
            'useStandardSpaceFont' => 'bool',
            'sequence' => '',
            'product' => '',
            'part' => '',
        ];
    }
}