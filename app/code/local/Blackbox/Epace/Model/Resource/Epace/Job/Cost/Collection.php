<?php

/**
 * @method Blackbox_Epace_Model_Epace_Job_Cost[] getItems()
 *
 * Class Blackbox_Epace_Model_Resource_Epace_Job_Cost_Collection
 */
class Blackbox_Epace_Model_Resource_Epace_Job_Cost_Collection extends Blackbox_Epace_Model_Resource_Epace_Collection
{
    protected function _construct()
    {
        $this->_init('efi/job_cost');
    }
}