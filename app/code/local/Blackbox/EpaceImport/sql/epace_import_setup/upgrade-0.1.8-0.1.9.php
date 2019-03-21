<?php

/** @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('epacei/estimate'), 'csr_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable'  => true,
    'comment'   => 'Sales Person Id'
));
$installer->getConnection()->addForeignKey($installer->getFkName('epacei/estimate', 'csr_id', 'customer/entity', 'entity_id'),
    $installer->getTable('epacei/estimate'), 'csr_id', $installer->getTable('customer/entity'), 'entity_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE);

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'csr_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable'  => true,
    'comment'   => 'Sales Person Id'
));
$installer->getConnection()->addForeignKey($installer->getFkName('sales/order', 'csr_id', 'customer/entity', 'entity_id'),
    $installer->getTable('sales/order'), 'csr_id', $installer->getTable('customer/entity'), 'entity_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE);

$installer->endSetup();
