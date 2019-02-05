<?php

class Blackbox_Epace_Model_Epace_Prepress_Size extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('PrepressSize', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'prepressItem' => 'float',
            'sizeWidth' => 'float',
            'sizeHeight' => 'float',
        ];
    }
}