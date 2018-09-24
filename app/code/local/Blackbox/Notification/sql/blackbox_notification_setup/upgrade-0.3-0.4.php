<?php
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

/**
 * Create table 'blackbox_notification/email_redirect'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('blackbox_notification/email_redirect'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Redirect Id')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, array(
        'nullable'  => false,
    ), 'Type')
    ->addColumn('params', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => true,
    ), 'Params')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Created At')
    ->setComment('Email Redirect');
$installer->getConnection()->createTable($table);

/**
 * Create table 'blackbox_notification/email_redirect_url'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('blackbox_notification/email_redirect_url'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Redirect Id')
    ->addColumn('url', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'Url')
    ->setComment('Email Redirect Url');
$installer->getConnection()->createTable($table);

/**
 * Create table 'blackbox_notification/email_redirect_url_map'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('blackbox_notification/email_redirect_url_map'))
    ->addColumn('redirect_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'unsigned'  => true,
    ), 'Redirect Id')
    ->addColumn('url_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'unsigned'  => true,
    ), 'Url Id')
    ->addColumn('group_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => true,
        'unsigned'  => true,
    ), 'Group Id')
    ->addIndex($installer->getIdxName('blackbox_notification/email_redirect_url_map', array('redirect_id', 'url_id', 'group_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY), array('redirect_id', 'url_id', 'group_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY)
    ->addForeignKey($installer->getFkName('blackbox_notification/email_redirect_url_map', 'redirect_id', 'blackbox_notification/email_redirect', 'id'),
        'redirect_id', $installer->getTable('blackbox_notification/email_redirect'), 'id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('blackbox_notification/email_redirect_url_map', 'url_id', 'blackbox_notification/email_redirect_url', 'id'),
        'url_id', $installer->getTable('blackbox_notification/email_redirect_url'), 'id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('blackbox_notification/email_redirect_url_map', 'group_id', 'customer/customer_group', 'customer_group_id'),
        'group_id', $installer->getTable('customer/customer_group'), 'customer_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Email Redirect Url');
$installer->getConnection()->createTable($table);

$installer->getConnection()->addColumn($installer->getTable('blackbox_notification/rule'), 'redirect_config', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'nullable' => true,
    'comment'   => 'Redirect Config'
));

$installer->endSetup();
