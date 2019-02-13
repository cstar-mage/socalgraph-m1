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
        return $this->_getObject('jobPart', $this->getJobPartKeyField(), 'efi/job_part');
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

    /**
     * @return string
     */
    public function getJobPartKey()
    {
        return $this->getData($this->getJobPartKeyField());
    }

    /**
     * @return string
     */
    public abstract function getJobPartKeyField();
}