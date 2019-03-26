<?php

/** @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'epace_job_description', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 255,
    'nullable'  => true,
    'comment'   => 'Epace Job Description'
));

$installer->endSetup();
