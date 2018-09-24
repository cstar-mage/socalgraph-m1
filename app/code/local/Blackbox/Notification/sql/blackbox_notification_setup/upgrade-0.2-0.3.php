<?php
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

/**
 * Create table 'blackbox_notification/log'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('blackbox_notification/log'))
    ->addColumn('notification_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Notification Id')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Rule Id')
    ->addColumn('params', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => true,
    ), 'Params')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Created At')
    ->setComment('Notification Log');
$installer->getConnection()->createTable($table);

$installer->endSetup();
