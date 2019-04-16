<?php

trait Blackbox_Epace_Model_Epace_Job_Part_ChildTrait
{
    /**
     * @return string
     */
    public function getJobPartNumber()
    {
        return $this->getData('jobPart');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Part|false
     */
    public function getJobPart()
    {
        return $this->_getObject('jobPart', $this->getJobPartKeyField(), 'efi/job_part');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Job_Part $part
     * @return $this
     */
    public function setJobPart(Blackbox_Epace_Model_Epace_Job_Part $part)
    {
        return $this->_setObject('jobPart', $part);
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Part|false
     * @deprecated
     */
    public function getPart()
    {
        return $this->getJobPart();
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Job_Part $part
     * @return $this
     * @deprecated
     */
    public function setPart(Blackbox_Epace_Model_Epace_Job_Part $part)
    {
        return $this->setJobPart($part);
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