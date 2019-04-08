<?php

/**
 * @method string getId()
 * @method string getDescription()
 * @method int getSalesTaxCalculationBasis()
 * @method bool getActive()
 * @method float getStateTaxRate()
 * @method float getStateTaxLimit()
 * @method bool getShowSSN()
 * @method bool getSeventhDayDoubletime()
 * @method bool getDoubleTimeAfter12Hours()
 * @method string getPrimaryKey()
 *
 * Class Blackbox_Epace_Model_Epace_State
 */
class Blackbox_Epace_Model_Epace_State extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('State', 'primaryKey');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Country
     */
    public function getCountry()
    {
        return $this->_getObject('country', 'country', 'efi/country', true);
    }

    public function getDefinition()
    {
        return [
            'id' => 'string',
            'description' => 'string',
            'country' => 'int',
            'salesTaxCalculationBasis' => 'int',
            'active' => 'bool',
            'stateTaxRate' => 'float',
            'stateTaxLimit' => 'float',
            'showSSN' => 'bool',
            'seventhDayDoubletime' => 'bool',
            'doubleTimeAfter12Hours' => 'bool',
            'primaryKey' => 'string',
        ];
    }
}