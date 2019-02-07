<?php

/**
 * @method string getName()
 * @method string getEmail()
 * @method string getNotes()
 * @method bool getActive()
 * @method string getPhoneNumber()
 *
 * Class Blackbox_Epace_Model_Epace_CSR
 */
class Blackbox_Epace_Model_Epace_CSR extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('CSR', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'name' => 'string',
            'email' => 'string',
            'notes' => 'string',
            'active' => 'bool',
            'phoneNumber' => 'string',
        ];
    }
}
