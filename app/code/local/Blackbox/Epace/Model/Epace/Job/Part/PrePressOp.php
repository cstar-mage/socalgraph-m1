<?php

class Blackbox_Epace_Model_Epace_Job_Part_PrePressOp extends Blackbox_Epace_Model_Epace_Job_Part_AbstractChild
{
    protected function _construct()
    {
        $this->_init('JobPartPrePressOp', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'description' => '',
            'quantity' => '',
            'ganged' => '',
            'numOut' => '',
            'unitLabel' => '',
            'prepressItem' => '',
            'sizeWidth' => '',
            'sizeHeight' => '',
            'prepActivity' => '',
            'state' => '',
            'job' => '',
            'jobPart' => '',
            'manual' => 'bool',
            'sequence' => '',
            'customerViewable' => 'bool',
            'size' => '',
            'JobPartKey' => '',
        ];
    }
}