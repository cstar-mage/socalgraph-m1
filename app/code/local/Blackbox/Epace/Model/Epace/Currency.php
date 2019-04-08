<?php

/**
 * @method string getIsoCurrencyId()
 * @method string getDisplayCode()
 * @method bool getDisplayCurrencyCode()
 * @method string getDescription()
 * @method bool getActive()
 * @method int getSequence()
 * @method string getSymbol()
 * @method string getDecimalSeperator()
 * @method string getGroupSeperator()
 * @method string getPatternSeperator()
 * @method int getGroupSize()
 * @method int getZeroDigit()
 * @method string getDigit()
 * @method int getSymbolLocation()
 * @method string getConversion()
 * @method string getPattern()
 * @method string getSample()
 * @method float getCurrentExchangeRate()
 *
 * Class Blackbox_Epace_Model_Epace_Currency
 */
class Blackbox_Epace_Model_Epace_Currency extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('Currency', 'isoCurrencyId');
    }

    public function getDefinition()
    {
        return [
            'isoCurrencyId' => 'string',
            'displayCode' => 'string',
            'displayCurrencyCode' => 'bool',
            'description' => 'string',
            'active' => 'bool',
            'sequence' => 'int',
            'symbol' => 'string',
            'decimalSeperator' => 'string',
            'groupSeperator' => 'string',
            'patternSeperator' => 'string',
            'groupSize' => 'int',
            'zeroDigit' => 'int',
            'digit' => 'string',
            'symbolLocation' => 'int',
            'conversion' => 'string',
            'pattern' => 'string',
            'sample' => 'string',
            'currentExchangeRate' => 'float',
        ];
    }
}