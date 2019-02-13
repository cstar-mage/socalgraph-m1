<?php

/**
 * @method string getCreatedBy()
 * @method string getCreaedDate()
 * @method string getCreatedTime()
 * @method string getDepartment()
 * @method string getNote()
 * @method bool getFromEstimating()
 * @method bool getFromQuote()
 * @method bool getCustomerViewable()
 * @method string getNoteSummary()
 *
 * Class Blackbox_Epace_Model_Epace_Job_Note
 */
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