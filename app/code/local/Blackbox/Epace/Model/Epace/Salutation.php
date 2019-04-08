<?php

/**
 * @method string getSalutation()
 *
 * Class Blackbox_Epace_Model_Epace_Salutation
 */
class Blackbox_Epace_Model_Epace_Salutation extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('Salutation', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'salutation' => 'string'
        ];
    }
}