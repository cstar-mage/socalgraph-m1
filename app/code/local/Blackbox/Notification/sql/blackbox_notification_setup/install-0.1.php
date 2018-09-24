<?php
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

/**
 * Create table 'blackbox_notification/rule'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('blackbox_notification/rule'))
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
    ->addColumn('email_sender', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
    ), 'Emails')
    ->addColumn('email_template_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Email Template Id')
    ->addColumn('emails', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
    ), 'Emails')
    ->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'Conditions Serialized')
    ->setComment('Notification Rule');
$installer->getConnection()->createTable($table);

$connection          = $installer->getConnection();

$rulesTable          = $installer->getTable('blackbox_notification/rule');
$websitesTable       = $installer->getTable('core/website');
$rulesWebsitesTable  = $installer->getTable('blackbox_notification/website');

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
            $installer->getIdxName('blackbox_notification/website', array('rule_id')),
            array('rule_id')
        )
        ->addIndex(
            $installer->getIdxName('blackbox_notification/website', array('website_id')),
            array('website_id')
        )
        ->addForeignKey($installer->getFkName('blackbox_notification/website', 'rule_id', 'blackbox_notification/rule', 'rule_id'),
            'rule_id', $rulesTable, 'rule_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->addForeignKey($installer->getFkName('blackbox_notification/website', 'website_id', 'core/website', 'website_id'),
            'website_id', $websitesTable, 'website_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->setComment('Notifications To Websites Relations');

    $connection->createTable($table);
}

$installer->endSetup();
