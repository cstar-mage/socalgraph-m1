<?php

class Blackbox_Epace_model_Epace_Change_Order extends Blackbox_Epace_Model_Epace_Job_Part_AbstractChild
{
    protected function _construct()
    {
        $this->_init('ChangeOrder', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Change_Order_Type|bool
     */
    public function getType()
    {
        return $this->_getObject('type', 'type', 'efi/change_order_type', true);
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Change_Order_Type $type
     * @return $this
     */
    public function setType(Blackbox_Epace_Model_Epace_Change_Order_Type $type)
    {
        return $this->_setObject('type', $type);
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'job' => 'string',
            'jobPart' => 'string',
            'department' => 'int',
            'num' => 'int',
            'entryDate' => 'string',
            'entryTime' => 'string',
            'description' => 'string',
            'enteredBy' => 'string',
            'type' => 'int',
            'billed' => 'bool',
            'taxAmount' => 'float',
            'taxBase' => 'float',
            'JobPartKey' => 'string',
        ];
    }
}