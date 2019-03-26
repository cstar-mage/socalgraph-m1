<?php

/** @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'epace_bill_to_job_contact', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable'  => true,
    'comment'   => 'Epace BillToJobContact'
));

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'epace_ship_to_job_contact', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable'  => true,
    'comment'   => 'Epace ShipToJobContact'
));

$installer->endSetup();
