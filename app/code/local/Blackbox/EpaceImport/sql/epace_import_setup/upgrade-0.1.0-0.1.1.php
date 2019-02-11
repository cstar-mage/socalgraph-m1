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

$installer->getConnection()->addColumn($installer->getTable('sales/shipment_item'), 'epace_job_', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable'  => true,
    'comment'   => 'Job Part Id'
));

$installer->endSetup();