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
        if (is_null($this->finishingOperation)) {
            $this->finishingOperation = false;
            if ($this->getData('finishingOperation')) {
                $operation = Mage::getModel('efi/finishingOperation')->load($this->getData('finishingOperation'));
                if ($operation->getId()) {
                    $this->finishingOperation = $operation;
                }
            }
        }

        return $this->finishingOperation;
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