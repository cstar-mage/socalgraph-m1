<?php

/* @var Mage_Eav_Model_Entity_Setup $this */
$this->startSetup();

$this->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'inventory_number',  array(
    'type'                       => 'varchar',
    'label'                      => 'Inventory Number',
    'input'                      => 'text',
    'backend'                    => '',
    'required'                   => false,
    'sort_order'                 => 255,
));

$this->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'barcode_data_source',  array(
    'type'                       => 'varchar',
    'input'                      => 'select',
    'source'                     => 'barcode/eav_entity_attribute_source_data',
    'label'                      => 'Barcode Data Source',
    'backend'                    => '',
    'required'                   => false,
    'sort_order'                 => 255,
    'visible'                    => false
));

$this->endSetup();