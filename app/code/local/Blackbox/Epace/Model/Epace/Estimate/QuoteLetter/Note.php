<?php

class Blackbox_Epace_Model_Epace_Estimate_QuoteLetter_Note extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('EstimateQuoteLetterNote', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate_QuoteLetter|bool
     */
    public function getEstimateQuoteLetter()
    {
        return $this->_getObject('estimateQuoteLetter', 'estimateQuoteLetter', 'efi/estimate_quoteLetter');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Estimate_QuoteLetter $quoteLetter
     * @return $this
     */
    public function setEstimateQuoteLetter(Blackbox_Epace_Model_Epace_Estimate_QuoteLetter $quoteLetter)
    {
        return $this->_setObject('estimateQuoteLetter', $quoteLetter);
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'estimateQuoteLetter' => 'int',
            'note' => 'string',
            'printOnReport' => 'bool',
            'section' => 'string',
            'category' => 'string',
            'useStandardSpaceFont' => 'bool',
            'sequence' => 'float',
            'product' => 'float',
            'part' => 'float',
        ];
    }
}