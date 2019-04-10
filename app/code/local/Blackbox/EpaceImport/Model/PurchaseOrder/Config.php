<?php

class Blackbox_EpaceImport_Model_PurchaseOrder_Config
{
    /**
     * Retrieve status label
     *
     * @param   string $code
     * @return  string
     */
    public function getStatusLabel($code)
    {
        return $this->getStatuses()[$code];
    }


    /**
     * Retrieve all statuses
     *
     * @return array
     */
    public function getStatuses()
    {
        $statuses = [
            'C' => 'Closed',
            'O' => 'Open',
            'P' => 'Pending',
            'R' => 'Received',
            'X' => 'Cancelled',
            'Z' => 'Reconciled'
        ];
        return $statuses;
    }
}
