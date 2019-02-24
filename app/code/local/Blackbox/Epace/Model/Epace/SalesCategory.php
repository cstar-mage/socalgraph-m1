<?php

class Blackbox_Epace_Model_Epace_SalesCategory extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('SalesCategory', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'name' => 'string',
            'taxable' => 'bool',
            'active' => 'bool',
            'commissionable' => 'bool',
            'taxReport' => 'bool',
            'salesReport' => 'bool',
            'glAccount' => 'int',
            'glDepartment' => 'int',
            'includeInDiscount' => 'bool',
            'commission' => 'bool',
        ];
    }
}