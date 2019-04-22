<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'original_order_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'length'    => 10,
    'unsigned'  => true,
    'nullable'  => true,
    'comment'   => 'Original Order Id'
));
$installer->getConnection()->addForeignKey($installer->getFkName('sales/order', 'original_order_id', 'sales/order', 'entity_id'),
    $installer->getTable('sales/order'), 'original_order_id', $installer->getTable('sales/order'), 'entity_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE);