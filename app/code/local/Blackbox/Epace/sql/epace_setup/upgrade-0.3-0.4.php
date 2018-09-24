<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$this->getConnection()->addColumn($this->getTable('sales/invoice'), 'epace_job_state', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
    'default'   => 0,
    'nullable'  => false,
    'comment'   => 'Epace Job State'
));

$installer->endSetup();