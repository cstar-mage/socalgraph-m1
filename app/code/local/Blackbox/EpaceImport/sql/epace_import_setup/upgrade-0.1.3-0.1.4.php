<?php

/** @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'base_markup', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'scale'     => 4,
    'precision' => 12,
    'nullable'  => true,
    'comment'   => 'Base Markup'
));

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'markup', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'scale'     => 4,
    'precision' => 12,
    'nullable'  => true,
    'comment'   => 'Markup'
));

$installer->endSetup();