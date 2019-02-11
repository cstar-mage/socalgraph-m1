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

    /**
     * @var Blackbox_Epace_Model_Epace_Job_Material
     */
    protected $jobMaterial;

    /**
     * @var Blackbox_Epace_Model_Epace_Job_Part_PressForm
     */
    protected $jobPartPressForm;

    /**
     * @var Blackbox_Epace_Model_Epace_Job_Component
     */
    protected $jobComponent;

    /**
     * @var
     */
    protected $proof;

    /**
     * @var
     */
    protected $jobPartItem;

    /**
     * @var bool
     */
    protected $linksInitialized = false;

    protected function _construct()
    {
        $this->_init('CartonContent', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Carton|bool
     */
    public function getCarton()
    {
        return $this->_getObject('carton', 'carton', 'efi/carton');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Carton $carton
     * @return $this
     */
    public function setCarton(Blackbox_Epace_Model_Epace_Carton $carton)
    {
        $this->carton = $carton;

        if (!$this->linksInitialized) {
            $this->_initLinks();
        }

        return $this;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job|bool
     */
    public function getJobPartJob()
    {
        return $this->_getObject('jobPartJob', 'jobPartJob', 'efi/job');
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

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Material|bool
     */
    public function getJobMaterial()
    {
        return $this->_getObject('jobMaterial', 'jobMaterial', 'efi/job_material');
    }

    public function setJobMaterial(Blackbox_Epace_Model_Epace_Job_Material $jobMaterial)
    {
        $this->jobMaterial = $jobMaterial;

        return $this;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Part_PressForm|bool
     */
    public function getJobPartPressForm()
    {
        return $this->_getObject('jobPartPressForm', 'jobPartPressForm', 'efi/job_part_pressForm');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Job_Part_PressForm $pressForm
     * @return $this
     */
    public function setJobPartPressForm(Blackbox_Epace_Model_Epace_Job_Part_PressForm $pressForm)
    {
        $this->jobPartPressForm = $pressForm;

        return $this;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Component|bool
     */
    public function getJobComponent()
    {
        return $this->_getObject('jobComponent', 'jobComponent', 'efi/job_component');
    }

    public function setJobComponent(Blackbox_Epace_Model_Epace_Job_Component $jobComponent)
    {
        $this->jobComponent = $jobComponent;

        return $this;
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'note' => 'string',
            'carton' => 'int',
            'quantity' => 'int',
            'job' => 'string',
            'jobProduct' => '',
            'jobPartJob' => 'string',
            'jobPart' => 'string',
            'jobPartPressForm' => 'int',
            'jobComponent' => 'int',
            'proof' => 'int',
            'content' => 'string',
            'JobPartKey' => 'string',
        ];
    }

    public function getJobPartKey()
    {
        return $this->getData('JobPartKey');
    }

    protected function _initLinks()
    {
        if ($this->carton && $this->carton->getShipment() && ($job = $this->carton->getShipment()->getJob())) {
            if (!$this->job && !empty($this->getData('job')) && $this->getData('job') == $job->getId()) {
                $this->job = $job;
            }
            if (!$this->jobPartJob && !empty($this->getData('jobPartJob')) && $this->getData('jobPartJob') == $job->getId()) {
                $this->jobPartJob = $job;
            }
        }

        $this->linksInitialized = true;
    }
}