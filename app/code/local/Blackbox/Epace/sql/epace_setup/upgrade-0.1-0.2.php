<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$this->getConnection()->addColumn($this->getTable('sales/order'), 'epace_job', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'comment'   => 'Epace Job Id'
));

$installer->endSetup();