<?php

trait Blackbox_Epace_Model_Epace_Estimate_Part_ChildTrait
{
    /**
     * @var Blackbox_Epace_Model_Epace_Estimate_Part
     */
    protected $estimatePart = null;

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate_Part|false
     */
    public function getPart()
    {
        return $this->_getObject('estimatePart', 'estimatePart', 'efi/estimate_part');
    }

    public function setPart(Blackbox_Epace_Model_Epace_Estimate_Part $part)
    {
        $this->estimatePart = $part;

        return $this;
    }
}