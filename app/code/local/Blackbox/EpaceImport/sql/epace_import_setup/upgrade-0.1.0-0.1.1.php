<?php

/** @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('epacei/estimate_item'), 'epace_estimate_part_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable'  => true,
    'comment'   => 'Estimate Part Id'
));
$installer->getConnection()->addColumn($installer->getTable('epacei/estimate_item'), 'epace_estimate_quantity_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable'  => true,
    'comment'   => 'Estimate Quantity Id'
));

$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'epace_job_part', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 5,
    'nullable'  => true,
    'comment'   => 'Job Part'
));
$installer->getConnection()->addIndex($installer->getTable('sales/order_item'),
    $installer->getIdxName('sales/order', 'epace_job_part', Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
    ['epace_job_part'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX);

$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'epace_part_original_price', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'scale'     => 4,
    'precision' => 12,
    'nullable'  => true,
    'comment'   => 'Job Part Original Quoted Price'
));

$installer->getConnection()->addColumn($installer->getTable('sales/order_address'), 'epace_job_contact_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable'  => true,
    'comment'   => 'Job Contact Id'
));
$installer->getConnection()->addColumn($installer->getTable('sales/order_address'), 'epace_contact_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable'  => true,
    'comment'   => 'Epace Contact Id'
));

$installer->getConnection()->addColumn($installer->getTable('sales/shipment'), 'epace_shipment_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable'  => true,
    'comment'   => 'Epace Shipment Id'
));

$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'epace_invoice_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable'  => true,
    'comment'   => 'Epace Invoice Id'
));

$installer->endSetup();