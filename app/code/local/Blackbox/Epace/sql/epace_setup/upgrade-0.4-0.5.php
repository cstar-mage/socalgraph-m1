<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

if ($installer->getConnection()->isTableExists($this->getTable('storelocator/storelocator'))) {
    $this->getConnection()->addColumn($this->getTable('storelocator/storelocator'), 'epace_customer_id', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 10,
        'nullable' => true,
        'comment' => 'Epace Customer Id'
    ));
}

$installer->endSetup();