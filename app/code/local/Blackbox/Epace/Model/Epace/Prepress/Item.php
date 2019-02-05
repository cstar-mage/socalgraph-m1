<?php

class Blackbox_Epace_Model_Epace_Prepress_Item extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('PrepressItem', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'code' => '',
            'prepressType' => '',
            'jdfPrepressType' => '',
            'activityCodeLabor' => '',
            'activityCodeMaterials' => '',
            'description' => '',
            'minColors' => '',
            'maxColors' => '',
            'unitLabel' => '',
            'numFlats' => '',
            'active' => 'bool',
            'jdfDefaultItem' => 'bool',
            'useSizes' => 'bool',
            'sequence' => '',
            'prepressGroup' => '',
            'customerViewable' => 'bool',
        ];
    }
}