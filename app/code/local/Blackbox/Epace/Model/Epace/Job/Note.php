<?php

class Blackbox_Epace_Model_Epace_Job_Note extends Blackbox_Epace_Model_Epace_Job_AbstractChild
{
    protected function _construct()
    {
        $this->_init('JobNote', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'job' => '',
            'createdBy' => '',
            'createdDate' => '',
            'createdTime' => '',
            'department' => '',
            'note' => '',
            'fromEstimating' => 'bool',
            'fromQuote' => 'bool',
            'customerViewable' => 'bool',
            'noteSummary' => '',
        ];
    }
}