<?php

class Blackbox_Epace_Model_Epace_Job_Part_SizeAllowance extends Blackbox_Epace_Model_Epace_Job_Part_AbstractChild
{
    /**
     * @var Blackbox_Epace_Model_Epace_SizeAllowanceType
     */
    protected $sizeAllowanceType;

    protected function _construct()
    {
        $this->_init('JobPartSizeAllowance', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_SizeAllowanceType
     */
    public function getSizeAllowanceType()
    {
        if (is_null($this->sizeAllowanceType)) {
            $this->sizeAllowanceType = false;
            if ($this->getData('sizeAllowanceType')) {
                $type = Mage::helper('epace/object')->load('efi/sizeAllowanceType', $this->getData('sizeAllowanceType'));
                if ($type->getId()) {
                    $this->sizeAllowanceType = $type;
                }
            }
        }

        return $this->sizeAllowanceType;
    }

    /**
     * @param Blackbox_Epace_Model_Epace_SizeAllowanceType $type
     * @return $this
     */
    public function setSizeAllowanceType(Blackbox_Epace_Model_Epace_SizeAllowanceType $type)
    {
        $this->sizeAllowanceType = $type;

        return $this;
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'job' => '',
            'jobPart' => '',
            'sizeAllowanceType' => '',
            'head' => '',
            'headAllowanceExpression' => '',
            'spine' => '',
            'spineAllowanceExpression' => '',
            'face' => '',
            'faceAllowanceExpression' => '',
            'foot' => '',
            'footAllowanceExpression' => '',
            'numOddPanelsSpine' => '',
            'numOddPanelsWidth' => '',
            'oddPanelSpineSize' => '',
            'oddPanelWidthSize' => '',
            'spineWidth' => '',
            'calculatedHead' => '',
            'calculatedFoot' => '',
            'calculatedSpine' => '',
            'calculatedFace' => '',
            'JobPartKey' => '',
        ];
    }
}