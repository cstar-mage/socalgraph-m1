<?php

abstract class Blackbox_Epace_Model_Epace_Estimate_AbstractChild extends Blackbox_Epace_Model_Epace_AbstractObject
{
    /**
     * @var Blackbox_Epace_Model_Epace_Estimate
     */
    protected $estimate = null;

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate|false
     */
    public function getEstimate()
    {
        return $this->_getObject('estimate', 'estimate', 'efi/estimate');
    }

    public function setEstimate(Blackbox_Epace_Model_Epace_Estimate $estimate)
    {
        $this->estimate = $estimate;

        return $this;
    }
}