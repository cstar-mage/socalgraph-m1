<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'wizkunde_websso_claim'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('websso/session'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'ID')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
    ), 'Customer ID')
    ->addColumn('session_id', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
    ), 'Session ID')
    ->addColumn('sso_session', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => true,
    ), 'SSO Session');
$installer->getConnection()->createTable($table);

$installer->endSetup();
