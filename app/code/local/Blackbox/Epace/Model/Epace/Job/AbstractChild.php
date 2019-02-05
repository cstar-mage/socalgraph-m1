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
        if (is_null($this->job)) {
            $job = Mage::getModel('efi/job')->load($this->getData('job'));
            if ($job->getId()) {
                $this->job = $job;
            } else {
                $this->job = false;
            }
        }

        return $this->job;
    }

    public function setJob(Blackbox_Epace_Model_Epace_Job $job)
    {
        $this->job = $job;

        return $this;
    }
}