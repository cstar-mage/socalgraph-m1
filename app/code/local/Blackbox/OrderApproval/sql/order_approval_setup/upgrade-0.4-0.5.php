<?php

$installer = $this; /* @var Mage_Eav_Model_Entity_Setup $installer */

$installer->startSetup();

/**
 * Create table 'order_approval/disapproval'
 */
$installer->getConnection()->dropTable($installer->getTable('order_approval/disapproval'));
$table = $installer->getConnection()
    ->newTable($installer->getTable('order_approval/disapproval'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'identity'  => true
    ), 'Entity Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Store Id')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Order Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => true,
        'default'   => null,
    ), 'Customer ID')
    ->addColumn('email_sent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Email Sent')
    ->addColumn('state', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'State')
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'approval Rule Id')
    ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'approvald by User Id')
    ->addColumn('order_increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
    ), 'Order Increment Id')
    ->addColumn('comment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
    ), 'Comment Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Updated At')
    ->addIndex($installer->getIdxName('order_approval/disapproval', array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName('order_approval/disapproval', array('order_id')),
        array('order_id'))
    ->addIndex($installer->getIdxName('order_approval/disapproval', array('state')),
        array('state'))
    ->addIndex($installer->getIdxName('order_approval/disapproval', array('created_at')),
        array('created_at'))
    ->addForeignKey($installer->getFkName('order_approval/disapproval', 'order_id', 'sales/order', 'entity_id'),
        'order_id', $installer->getTable('sales/order'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('order_approval/disapproval', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('order_approval/disapproval', 'user_id', 'customer/entity', 'entity_id'),
        'user_id', $installer->getTable('customer/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('order_approval/disapproval', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id', $installer->getTable('customer/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('order_approval/disapproval', 'rule_id', 'order_approval/rule', 'rule_id'),
        'rule_id', $installer->getTable('order_approval/rule'), 'rule_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('order_approval/disapproval', 'comment_id', 'sales/order_status_history', 'entity_id'),
        'comment_id', $installer->getTable('sales/order_status_history'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Order Disapproval');
$installer->getConnection()->createTable($table);

$installer->endSetup();
