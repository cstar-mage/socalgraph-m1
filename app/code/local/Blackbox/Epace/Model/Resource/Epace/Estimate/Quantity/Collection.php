<?php

/**
 * @method Blackbox_Epace_Model_Epace_Estimate_Quantity[] getItems()
 *
 * Class Blackbox_Epace_Model_Resource_Epace_Estimate_Quantity_Collection
 */
class Blackbox_Epace_Model_Resource_Epace_Estimate_Quantity_Collection extends Blackbox_Epace_Model_Resource_Epace_Collection
{
    protected function _construct()
    {
        $this->_init('efi/estimate_quantity');
    }
}