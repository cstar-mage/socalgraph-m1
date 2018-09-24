<?php
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

/**
 * Create table 'order_approval/rule'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('order_approval/rule'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rule Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Name')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Description')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Active')
    ->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'Conditions Serialized')
    ->addColumn('actions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'Actions Serialized')
    ->setComment('Order Approval Rule');
$installer->getConnection()->createTable($table);

$connection          = $installer->getConnection();

$rulesTable          = $installer->getTable('order_approval/rule');
$websitesTable       = $installer->getTable('core/website');
$rulesWebsitesTable  = $installer->getTable('order_approval/website');

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
            $installer->getIdxName('order_approval/website', array('rule_id')),
            array('rule_id')
        )
        ->addIndex(
            $installer->getIdxName('order_approval/website', array('website_id')),
            array('website_id')
        )
        ->addForeignKey($installer->getFkName('order_approval/website', 'rule_id', 'order_approval/rule', 'rule_id'),
            'rule_id', $rulesTable, 'rule_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->addForeignKey($installer->getFkName('order_approval/website', 'website_id', 'core/website', 'website_id'),
            'website_id', $websitesTable, 'website_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->setComment('Catalog Rules To Websites Relations');

    $connection->createTable($table);
}

$installer->endSetup();
