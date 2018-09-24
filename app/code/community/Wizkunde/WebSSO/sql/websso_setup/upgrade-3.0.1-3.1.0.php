<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**alter
 * Create table 'wizkunde_websso_claim'
 */
$installer->getConnection()
    ->addColumn($installer->getTable('websso/idp'), 'sso_binding', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
        'nullable'  => false,
        'comment'   => 'SSO Binding')
    );

$installer->getConnection()
    ->addColumn($installer->getTable('websso/idp'), 'slo_binding', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
        'nullable'  => false,
        'comment'   => 'SLO Binding')
    );

$installer->getConnection()
    ->addColumn($installer->getTable('websso/idp'), 'log_claims', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
        'nullable'  => false,
        'comment'   => 'Enable Claim Logging')
    );

$installer->getConnection()
    ->addColumn($installer->getTable('websso/idp'), 'log_debug', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
        'nullable'  => false,
        'comment'   => 'Enable Debug Logging')
    )
;

$installer->endSetup();