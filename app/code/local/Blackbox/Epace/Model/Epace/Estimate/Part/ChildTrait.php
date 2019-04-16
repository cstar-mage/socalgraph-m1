<?php

trait Blackbox_Epace_Model_Epace_Estimate_Part_ChildTrait
{
    /**
     * @return int
     */
    public function getEstimatePartId()
    {
        return $this->getData('estimatePart');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate_Part|false
     */
    public function getEstimatePart()
    {
        return $this->_getObject('estimatePart', 'estimatePart', 'efi/estimate_part');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Estimate_Part $part
     * @return $this
     */
    public function setEstimatePart(Blackbox_Epace_Model_Epace_Estimate_Part $part)
    {
        return $this->_setObject('estimatePart', $part);
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Estimate_Part|false
     * @deprecated
     */
    public function getPart()
    {
        return $this->getEstimatePart();
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Estimate_Part $part
     * @return $this
     * @deprecated
     */
    public function setPart(Blackbox_Epace_Model_Epace_Estimate_Part $part)
    {
        return $this->setEstimatePart($part);
    }
}