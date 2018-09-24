<?php

/* @var Mage_Catalog_Model_Resource_Setup $this */
$this->startSetup();

$this->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'step_count',  array(
    'type'     => 'int',
    'backend'  => '',
    'label'    => 'Qty in pack',
    'input'    => 'text',
    'source'   => '',
    'visible'  => true,
    'required' => false,
    'default' => '1',
    'frontend' => '',
    'unique'     => false,
    'sort_order' => 254
));

$this->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'step_count',  array(
    'used_in_product_listing'  => '1',
));

$entities = array(
    'quote_item',
    'order_item',
    'invoice_item',
    'shipment_item'
);
$options = array(
    'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'visible'  => true,
    'required' => false,
    'nullable' => false,
    'default' => 1
);

$installer = new Mage_Sales_Model_Resource_Setup('core_setup');

foreach ($entities as $entity) {
    $installer->addAttribute($entity, 'step_count', $options);
}

$this->endSetup();