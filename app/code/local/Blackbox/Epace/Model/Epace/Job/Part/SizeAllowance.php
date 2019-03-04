<?php

class Blackbox_Epace_Model_Epace_Job_Part_SizeAllowance extends Blackbox_Epace_Model_Epace_Job_Part_AbstractChild
{
    protected function _construct()
    {
        $this->_init('JobPartSizeAllowance', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_SizeAllowanceType
     */
    public function getSizeAllowanceType()
    {
        return $this->_getObject('sizeAllowanceType', 'sizeAllowanceType', 'efi/sizeAllowanceType', true);
    }

    /**
     * @param Blackbox_Epace_Model_Epace_SizeAllowanceType $type
     * @return $this
     */
    public function setSizeAllowanceType(Blackbox_Epace_Model_Epace_SizeAllowanceType $type)
    {
        return $this->_setObject('sizeAllowanceType', $type);
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