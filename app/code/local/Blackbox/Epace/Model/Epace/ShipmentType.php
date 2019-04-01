<?php

class Blackbox_Epace_Model_Epace_ShipmentType extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('ShipmentType', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'description' => '',
            'printLabel' => 'bool',
            'printDelTicket' => 'bool',
            'addQty' => 'bool',
            'status' => 'string',
            'defaultContact' => '',
            'showInEService' => 'bool',
            'shipToInventory' => 'bool',
            'freightActivityCode' => '',
            'planned' => 'bool',
            'packageDrop' => 'bool',
            'updateShipmentDate' => 'bool',
            'allowedOnCreditHold' => 'bool',
            'printableShipped' => 'bool',
            'active' => 'bool',
            'printCartonPackingSlip' => 'bool',
            'printSkidTag' => 'bool',
            'generateAdvancedShippingNotice' => 'bool',
            'webShipment' => 'bool',
            'linkShippedPlanned' => 'bool',
            'createInventoryReceipt' => 'bool',
            'updateInventoryItemCost' => 'bool',
        ];
    }
}