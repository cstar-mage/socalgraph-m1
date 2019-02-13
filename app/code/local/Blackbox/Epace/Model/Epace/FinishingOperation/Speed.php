<?php

class Blackbox_Epace_Model_Epace_FinishingOperation_Speed extends Blackbox_Epace_Model_Epace_AbstractObject
{
    /**
     * @var Blackbox_Epace_Model_Epace_FinishingOperation
     */
    protected $finishingOperation;

    protected function _construct()
    {
        $this->_init('FinishingOperationSpeed', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_FinishingOperation|false
     */
    public function getFinisingOperation()
    {
        return $this->_getObject('finishingOperation', 'finishingOperation', 'efi/finishingOperation');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_FinishingOperation $operation
     * @return $this
     */
    public function setFinishingOperation(Blackbox_Epace_Model_Epace_FinishingOperation $operation)
    {
        $this->finishingOperation = $operation;

        return $this;
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'finishingOperation' => 'int',
            'quantity' => 'float',
            'unitsPerHour' => 'float',
            'spoilage' => 'float',
            'costPerM' => 'float',
        ];
    }
}