<?php
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('admin/user'), 'customer_id', array(
    'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable' => true,
    'default' => null,
    'comment' => 'Customer Id'
));

$installer->endSetup();
