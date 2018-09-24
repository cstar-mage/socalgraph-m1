<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'wizkunde_websso_claim'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('websso/claim'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'ID')
    ->addColumn('external', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
    ), 'External Attribute')
    ->addColumn('internal', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
    ), 'Magento Attribute');
$installer->getConnection()->createTable($table);

$installer->endSetup();