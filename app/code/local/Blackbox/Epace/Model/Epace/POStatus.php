<?php

/**
 * @method string getId()
 * @method string getDescription()
 *
 * Class Blackbox_Epace_Model_Epace_POStatus
 */
class Blackbox_Epace_Model_Epace_POStatus extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('POStatus', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'string',
            'description' => 'string'
        ];
    }
}