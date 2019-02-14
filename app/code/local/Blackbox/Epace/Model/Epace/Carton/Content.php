<?php

/**
 * @method string getNote()
 * @method int getQuantity()
 * @method string getContent()
 *
 * Class Blackbox_Epace_Model_Epace_Carton_Content
 */
class Blackbox_Epace_Model_Epace_Carton_Content extends Blackbox_Epace_Model_Epace_AbstractObject
{
    use Blackbox_Epace_Model_Epace_Job_Part_ChildTrait;

    /**
     * @var Blackbox_Epace_Model_Epace_Job
     */
    protected $job = null;

    /**
     * @var Blackbox_Epace_Model_Epace_Job_Product
     */
    protected $jobProduct = null;

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

        return $this;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job|bool
     */
    public function getJob()
    {
        return $this->_getObject('job', 'job', 'efi/job');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Job $job
     * @return $this
     */
    public function setJob(Blackbox_Epace_Model_Epace_Job $job)
    {
        $this->job = $job;

        return $this;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Product
     */
    public function getJobProduct()
    {
        return $this->_getObject('jobProduct', 'jobProduct', 'efi/job_product');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Job_Product $product
     * @return $this
     */
    public function setJobProduct(Blackbox_Epace_Model_Epace_Job_Product $product)
    {
        $this->jobProduct = $product;

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
     * @return Blackbox_Epace_Model_Epace_Job_Part|bool
     */
    public function getJobPart()
    {
        return $this->_getObject('jobPart', 'jobPart', 'efi/job_part');
    }

    public function setJobPart(Blackbox_Epace_Model_Epace_Job_Part $part)
    {
        $this->jobPart = $part;

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

    /**
     * @param Blackbox_Epace_Model_Epace_Job_Component $jobComponent
     * @return $this
     */
    public function setJobComponent(Blackbox_Epace_Model_Epace_Job_Component $jobComponent)
    {
        $this->jobComponent = $jobComponent;

        return $this;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Proof|bool
     */
    public function getProof()
    {
        return $this->_getObject('proof', 'proof', 'efi/proof');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Proof $proof
     * @return $this
     */
    public function setProof(Blackbox_Epace_Model_Epace_Proof $proof)
    {
        $this->proof = $proof;

        return $this;
    }

    /**
     * @return Blackbox_Epace_Model_Epace_Job_Part_Item|bool
     */
    public function getJobPartItem()
    {
        return $this->_getObject('jobPartItem', 'jobPartItem', 'efi/job_part_item');
    }

    /**
     * @param Blackbox_Epace_Model_Epace_Job_Part_Item $item
     * @return $this
     */
    public function setJobPartItem(Blackbox_Epace_Model_Epace_Job_Part_Item $item)
    {
        $this->jobPartItem = $item;

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
            'jobMaterial' => 'int',
            'jobPartPressForm' => 'int',
            'jobComponent' => 'int',
            'proof' => 'int',
            'content' => 'string',
            'JobPartKey' => 'string',
        ];
    }

    public function getJobPartKeyField()
    {
        return 'JobPartKey';
    }
}