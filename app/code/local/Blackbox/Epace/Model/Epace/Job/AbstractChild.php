<?php

abstract class Blackbox_Epace_Model_Epace_Job_AbstractChild extends Blackbox_Epace_Model_Epace_AbstractObject
{
    /**
     * @var Blackbox_Epace_Model_Epace_Job
     */
    protected $job = null;

    /**
     * @return Blackbox_Epace_Model_Epace_Job|false
     */
    public function getJob()
    {
        return $this->_getObject('job', 'job', 'efi/job');
    }

    public function setJob(Blackbox_Epace_Model_Epace_Job $job)
    {
        $this->job = $job;

        return $this;
    }
}