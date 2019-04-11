<?php

/** @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'epace_invoice_number', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 255,
    'nullable'  => true,
    'comment'   => 'Invoice Number'
));

$installer->getConnection()->addColumn($installer->getTable('epacei/receivable'), 'invoice_number', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 255,
    'nullable'  => true,
    'comment'   => 'Invoice Number'
));

$installer->getConnection()->dropColumn($installer->getTable('epacei/receivable'), 'invoice_id');

$installer->endSetup();
