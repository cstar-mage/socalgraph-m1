<?php

trait Blackbox_Epace_Model_Epace_Job_Part_ChildTrait
{
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
        return $this->_setObject('jobPart', $part);
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