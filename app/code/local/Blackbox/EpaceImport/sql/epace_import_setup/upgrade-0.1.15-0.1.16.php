<?php

/** @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'receivable_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'unsigned'  => true,
    'nullable'  => true,
    'comment'   => 'Receivable Id'
));
$installer->getConnection()->addForeignKey($installer->getFkName('sales/invoice', 'receivable_id', 'epacei/receivable', 'entity_id'),
    $installer->getTable('sales/invoice'), 'receivable_id', $installer->getTable('epacei/receivable'), 'entity_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE);

$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'epace_receivable_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'unsigned'  => true,
    'nullable'  => true,
    'comment'   => 'Epace Receivable Id'
));

$installer->endSetup();
