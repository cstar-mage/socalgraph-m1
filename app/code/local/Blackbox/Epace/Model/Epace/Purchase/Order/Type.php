<?php

/**
 * @method int getId()
 * @method string getDescription()
 * @method int getPoNumberPrefix()
 * @method int getPoNumberSequence()
 * @method bool getAutoNumberOnly()
 * @method bool getActive()
 * @method bool getUseManufacturingLocationPrefix()
 *
 * Class Blackbox_Epace_Model_Epace_Purchase_Order_Type
 */
class Blackbox_Epace_Model_Epace_Purchase_Order_Type extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('PurchaseOrderType', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'description' => 'string',
            'poNumberPrefix' => 'int',
            'poNumberSequence' => 'int',
            'autoNumberOnly' => 'bool',
            'active' => 'bool',
            'useManufacturingLocationPrefix' => 'bool',
        ];
    }
}