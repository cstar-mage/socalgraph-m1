<?php
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

/**
 * Create table 'blackbox_notification/notification_rule'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('blackbox_notification/notification_rule'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Rule Id')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Rule Type')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Name')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
    ), 'Description')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
    ), 'Is Active')
    ->addColumn('content_template', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
    ), 'Content Template')
    ->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
    ), 'Conditions Serialized')
    ->setComment('Head Notification Rule');
$installer->getConnection()->createTable($table);

$connection          = $installer->getConnection();

$rulesTable          = $installer->getTable('blackbox_notification/notification_rule');
$websitesTable       = $installer->getTable('core/website');
$rulesWebsitesTable  = $installer->getTable('blackbox_notification/notification_website');

if (!$connection->isTableExists($rulesWebsitesTable)) {
    $table = $connection->newTable($rulesWebsitesTable)
        ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true
        ),
            'Rule Id'
        )
        ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true
        ),
            'Website Id'
        )
        ->addIndex(
            $installer->getIdxName('blackbox_notification/notification_website', array('rule_id')),
            array('rule_id')
        )
        ->addIndex(
            $installer->getIdxName('blackbox_notification/notification_website', array('website_id')),
            array('website_id')
        )
        ->addForeignKey($installer->getFkName('blackbox_notification/notification_website', 'rule_id', 'blackbox_notification/notification_rule', 'rule_id'),
            'rule_id', $rulesTable, 'rule_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->addForeignKey($installer->getFkName('blackbox_notification/notification_website', 'website_id', 'core/website', 'website_id'),
            'website_id', $websitesTable, 'website_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->setComment('Head Notifications To Websites Relations');

    $connection->createTable($table);
}

/**
 * Create table 'blackbox_notification/notification'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('blackbox_notification/notification'))
    ->addColumn('notification_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Notification Id')
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Rule Id')
    ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
    ), 'Content')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Customer Id')
    ->addColumn('viewed', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable'  => false,
        'default' => 0
    ), 'Customer Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Created At')
    ->addForeignKey($installer->getFkName('blackbox_notification/notification', 'rule_id', 'blackbox_notification/notification_rule', 'rule_id'),
        'rule_id', $rulesTable, 'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey($installer->getFkName('blackbox_notification/notification', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id', $installer->getTable('customer/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Notification');
$installer->getConnection()->createTable($table);

$installer->endSetup();