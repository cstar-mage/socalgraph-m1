<?php

/**
 * @method string getName()
 * @method string getEmail()
 * @method float getAnnualQuota()
 * @method bool getActive()
 * @method float getComissionRate()
 *
 * Class Blackbox_Epace_Model_Epace_SalesPerson
 */
class Blackbox_Epace_Model_Epace_SalesPerson extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('SalesPerson', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'name' => '',
            'email' => '',
            'annualQuota' => '',
            'active' => 'bool',
            'commissionRate' => '',
        ];
    }
}
