<?php

class Blackbox_Epace_Model_Epace_Change_Order_Type extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('ChangeOrderType', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'description' => 'string',
            'billable' => 'bool',
            'postageAdvance' => 'bool'
        ];
    }
}