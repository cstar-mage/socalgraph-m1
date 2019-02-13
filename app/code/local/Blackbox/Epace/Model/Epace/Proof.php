<?php

class Blackbox_Epace_Model_Epace_Proof extends Blackbox_Epace_Model_Epace_Job_Part_AbstractChild
{
    protected function _construct()
    {
        $this->_init('Proof', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'job' => 'string',
            'jobPart' => 'string',
            'description' => 'string',
            'status' => 'int',
            'notes' => 'string',
            'requestedBy' => 'string',
            'jobPartKey' => 'string',
        ];
    }
}