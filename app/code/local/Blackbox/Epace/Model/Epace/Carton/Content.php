<?php

class Blackbox_Epace_Model_Epace_Carton_Content extends Blackbox_Epace_Model_Epace_AbstractObject
{
    use Blackbox_Epace_Model_Epace_Job_Part_ChildTrait;

    /**
     * @var Blackbox_Epace_Model_Epace_Carton
     */
    protected $carton = null;

    /**
     * @var Blackbox_Epace_Model_Epace_Job
     */
    protected $jobPartJob = null;

    protected function _construct()
    {
        $this->_init('CartonContent', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Carton|bool
     */
    public function getCarton()
    {
        if (is_null($this->carton)) {
            $this->carton = false;
            if ($this->getData('carton')) {
                $carton = Mage::getModel('efi/carton')->load($this->getData('carton'));
                if ($carton->getId()) {
                    $this->carton = $carton;
                }
            }
        }

        return $this->carton;
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Carton $carton
     * @return $this
     */
    public function setCarton(Blackbox_Epace_Model_Epace_Carton $carton)
    {
        $this->carton = $carton;

        return $this;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job|bool
     */
    public function getJobPartJob()
    {
        if (is_null($this->jobPartJob)) {
            $this->jobPartJob = false;
            if ($this->getData('jobPartJob')) {
                $job = Mage::getModel('efi/job')->load($this->getData('jobPartJob'));
                if ($job->getId()) {
                    $this->jobPartJob = $job;
                }
            }
        }

        return $this->jobPartJob;
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Job $job
     * @return $this
     */
    public function setJobPartJob(Blackbox_Epace_Model_Epace_Job $job)
    {
        $this->jobPartJob = $job;

        return $this;
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'carton' => '',
            'quantity' => '',
            'jobPartJob' => '',
            'jobPart' => '',
            'content' => 'string',
            'JobPartKey' => '',
        ];
    }
}