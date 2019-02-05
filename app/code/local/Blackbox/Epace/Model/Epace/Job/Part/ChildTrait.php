<?php

trait Blackbox_Epace_Model_Epace_Job_Part_ChildTrait
{
    /**
     * @var Blackbox_Epace_Model_Epace_Job_Part
     */
    protected $jobPart = null;

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Part|false
     */
    public function getPart()
    {
        if (is_null($this->jobPart)) {
            $jobPart = Mage::getModel('efi/job_part')->load($this->getData('JobPartKey'));
            if ($jobPart->getId()) {
                $this->jobPart = $jobPart;
            } else {
                $this->jobPart = false;
            }
        }

        return $this->jobPart;
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Job_Part $part
     * @return $this
     */
    public function setPart(Blackbox_Epace_Model_Epace_Job_Part $part)
    {
        $this->jobPart = $part;

        return $this;
    }
}