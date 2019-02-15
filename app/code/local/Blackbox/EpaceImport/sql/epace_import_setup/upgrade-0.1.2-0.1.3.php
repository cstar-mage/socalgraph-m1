<?php

/** @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'job_type', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable'  => true,
    'comment'   => 'Job Type'
));

$installer->endSetup();