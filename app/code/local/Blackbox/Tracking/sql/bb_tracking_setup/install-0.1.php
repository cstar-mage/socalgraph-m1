<?php

/* @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('sales/shipment_track'), 'tracking_status', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'unsgigned' => true,
    'nullable'  => true,
    'comment'   => 'Tracking Status'
));
$installer->getConnection()->addColumn($installer->getTable('sales/shipment_track'), 'last_event', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 255,
    'unsgigned' => true,
    'nullable'  => true,
    'comment'   => 'Last Event'
));

$installer->endSetup();
