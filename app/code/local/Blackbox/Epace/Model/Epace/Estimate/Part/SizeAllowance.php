<?php

class Blackbox_Epace_Model_Epace_Estimate_Part_SizeAllowance extends Blackbox_Epace_Model_Epace_AbstractObject
{
    use Blackbox_Epace_Model_Epace_Estimate_Part_ChildTrait;

    protected function _construct()
    {
        $this->_init('EstimatePartSizeAllowance', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'estimatePart' => '',
            'state' => '',
            'source' => '',
            'sizeAllowanceType' => '',
            'head' => '',
            'spine' => '',
            'face' => '',
            'foot' => '',
            'numOddPanelsSpine' => '',
            'numOddPanelsWidth' => '',
            'oddPanelSpineSize' => '',
            'oddPanelWidthSize' => '',
            'spineWidth' => '',
            'spineWidthForced' => '',
            'headAllowanceExpression' => '',
            'footAllowanceExpression' => '',
            'spineAllowanceExpression' => '',
            'faceAllowanceExpression' => '',
            'calculatedHead' => '',
            'calculatedFoot' => '',
            'calculatedSpine' => '',
            'calculatedFace' => '',
        ];
    }
}