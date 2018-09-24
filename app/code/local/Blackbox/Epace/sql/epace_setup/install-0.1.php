<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('epace/event'))
    ->addColumn(
        'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true,
        ), 'Unique identifier'
    )
    ->addColumn(
        'name', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(), 'Event name'
    )
    ->addColumn(
        'processed_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Processed time'
    )
    ->addColumn(
        'status', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(), 'Status'
    )
    ->addColumn(
        'host', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(), 'Host'
    )
    ->addColumn(
        'username', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(), 'Username'
    )
    ->addColumn(
        'password', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(), 'Password'
    )
    ->addColumn(
        'mode', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(), 'Mode'
    )
    ->addColumn(
        'serialized_data', Varien_Db_Ddl_Table::TYPE_TEXT, 1000, array(), 'Serialized data'
    );

if (!$installer->getConnection()->isTableExists($table->getName())) {
    $installer->getConnection()->createTable($table);
}

$table = $installer->getConnection()
    ->newTable($installer->getTable('epace/event_file'))
    ->addColumn(
        'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true,
        ), 'Unique identifier'
    )
    ->addColumn(
        'event_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'unsigned' => true,
            'nullable' => false
        ), 'Event Id'
    )
    ->addColumn(
        'type', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(), 'File type'
    )
    ->addColumn(
        'action', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(), 'Action'
    )
    ->addColumn(
        'path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'File path'
    )
    ->addColumn(
        'related_file_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'unsigned' => true,
            'nullable' => true
        ), 'Related file'
    )
    ->addForeignKey(
        $installer->getFkName('epace/event_file', 'event_id', 'epace/event','id'),
        'event_id',
        $installer->getTable('epace/event'),
        'id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('epace/event_file', 'related_file_id', 'epace/event_file','id'),
        'related_file_id',
        $installer->getTable('epace/event_file'),
        'id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );

if (!$installer->getConnection()->isTableExists($table->getName())) {
    $installer->getConnection()->createTable($table);
}

$installer->endSetup();