<?php
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

$installer->getConnection()->addForeignKey($installer->getFkName('admin/user', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('admin/user'), 'customer_id', $installer->getTable('customer/entity'), 'entity_id',
    Varien_Db_Adapter_Interface::FK_ACTION_CASCADE, Varien_Db_Adapter_Interface::FK_ACTION_CASCADE);

$installer->endSetup();
