<?php

/** @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('epacei/estimate'), 'description', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 255,
    'nullable'  => false,
    'default'   => '',
    'comment'   => 'Description',
    'after'     => 'increment_id'
));

$installer->getConnection()->addColumn($installer->getTable('sales/shipment'), 'carrier', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 255,
    'nullable'  => true,
    'comment'   => 'Shipping Carrier'
));

$installer->getConnection()->addColumn($installer->getTable('sales/shipment'), 'carrier_title', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 255,
    'nullable'  => true,
    'comment'   => 'Shipping Carrier Title'
));

$installer->getConnection()->addColumn($installer->getTable('sales/shipment'), 'method', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 255,
    'nullable'  => true,
    'comment'   => 'Shipping Method'
));

$installer->getConnection()->addColumn($installer->getTable('sales/shipment'), 'method_title', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 255,
    'nullable'  => true,
    'comment'   => 'Shipping Method Title'
));

$installer->getConnection()->addColumn($installer->getTable('sales/shipment'), 'price', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'scale'     => 4,
    'precision' => 12,
    'nullable'  => true,
    'comment'   => 'Shipping Price'
));

$installer->getConnection()->addColumn($installer->getTable('sales/shipment'), 'cost', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'scale'     => 4,
    'precision' => 12,
    'nullable'  => true,
    'comment'   => 'Shipping Cost'
));

$installer->endSetup();
