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
        if (is_null($this->estimatePart)) {
            $estimatePart = Mage::getModel('efi/estimate_part')->load($this->getData('estimatePart'));
            if ($estimatePart->getId()) {
                $this->estimatePart = $estimatePart;
            } else {
                $this->estimatePart = false;
            }
        }

        return $this->estimatePart;
    }

    public function setPart(Blackbox_Epace_Model_Epace_Estimate_Part $part)
    {
        $this->estimatePart = $part;

        return $this;
    }
}