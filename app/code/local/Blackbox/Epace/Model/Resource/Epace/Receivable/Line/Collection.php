<?php

/**
 * @method Blackbox_Epace_Model_Epace_Receivable_Line[] getItems()
 *
 * Class Blackbox_Epace_Model_Resource_Epace_Receivable_Line_Collection
 */
class Blackbox_Epace_Model_Resource_Epace_Receivable_Line_Collection extends Blackbox_Epace_Model_Resource_Epace_Collection
{
    protected function _construct()
    {
        $this->_init('efi/receivable_line');
    }
}