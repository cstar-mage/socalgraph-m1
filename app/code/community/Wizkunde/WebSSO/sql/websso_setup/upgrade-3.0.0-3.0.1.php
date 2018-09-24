<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'wizkunde_websso_claim'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('websso/idp'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
    ), 'Name')
    ->addColumn('identifier', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
    ), 'Identifier')
    ->addColumn('name_id', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
    ), 'NameID')
    ->addColumn('metadata_url', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
    ), 'Metadata URL')
    ->addColumn('is_passive', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
    ), 'IsPassive')
    ->addColumn('metadata_expiration', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
    ), 'Expiration Time')
    ->addColumn('forceauthn', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
    ), 'ForceAuthN')
    ->addColumn('crt_data', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
    ), 'Certificate Data')
    ->addColumn('pem_data', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
    ), 'PEM Data')
    ->addColumn('passphrase', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
    ), 'Passphrase')
;
$installer->getConnection()->createTable($table);

$installer->endSetup();