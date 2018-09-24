<?php
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()->addColumn($installer->getTable('blackbox_notification/notification_rule'), 'customer_ids', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 255,
    'nullable' => false,
    'default' => '0',
    'comment' => 'Customer Ids'
));

$installer->endSetup();