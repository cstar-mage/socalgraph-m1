<?php

/**
 * @method string getIsoCountry()
 * @method string getIsoCountryAlpha3()
 * @method string getName()
 * @method string getDateFormatPattern()
 * @method string getTimeFormatPattern()
 * @method string getNumericFormatPattern()
 * @method bool getActive()
 * @method bool getStateRequired()
 * @method string getSequence()
 * @method bool getAllowVAT()
 * @method bool getDisplayStateCode()
 * @method int getIsoCountryNumber()
 * @method string getDateFormat()
 * @method string getTimeFormat()
 * @method string getNumericFormat()
 * @method string getDefaultISOCountryCode()
 *
 * Class Blackbox_Epace_Model_Epace_Country
 */
class Blackbox_Epace_Model_Epace_Country extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('Country', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => '',
            'isoCountry' => '',
            'isoCountryAlpha3' => '',
            'name' => '',
            'dateFormatPattern' => '',
            'timeFormatPattern' => '',
            'numericFormatPattern' => '',
            'active' => bool,
            'stateRequired' => bool,
            'sequence' => '',
            'allowVAT' => bool,
            'displayStateCode' => bool,
            'isoCountryNumber' => '',
            'dateFormat' => '',
            'timeFormat' => '',
            'numericFormat' => '',
            'defaultISOCountryCode' => '',
        ];
    }
}