<?php

/**
 * @method Blackbox_Epace_Model_Epace_Currency[] getItems()
 *
 * Class Blackbox_Epace_Model_Resource_Epace_Currency_Collection
 */
class Blackbox_Epace_Model_Resource_Epace_Currency_Collection extends Blackbox_Epace_Model_Resource_Epace_Collection
{
    protected function _construct()
    {
        $this->_init('efi/currency');
    }
}