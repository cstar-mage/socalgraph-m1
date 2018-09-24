<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop signature option from metadata
 */
$installer->getConnection()
    ->addColumn($installer->getTable('websso/claim'), 'server_id', array(
            'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'length'    => 40,
            'nullable'  => false,
            'comment'   => 'Server ID',
            'after'     => 'id'
    ));

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'websso/claim',
        'server_id',
        'websso/idp',
        'id'
    ),
    $installer->getTable('websso/claim'), 'server_id', $installer->getTable('websso/idp'), 'id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);

$installer->endSetup();
