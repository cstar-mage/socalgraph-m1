<?php

/**
 * @method Blackbox_Epace_Model_Epace_Invoice_Extra[] getItems()
 *
 * Class Blackbox_Epace_Model_Resource_Epace_Invoice_Extra_Collection
 */
class Blackbox_Epace_Model_Resource_Epace_Invoice_Extra_Collection extends Blackbox_Epace_Model_Resource_Epace_Collection
{
    protected function _construct()
    {
        $this->_init('efi/invoice_extra');
    }
}