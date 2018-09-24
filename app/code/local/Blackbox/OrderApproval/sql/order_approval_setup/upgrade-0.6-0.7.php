<?php

/* @var Mage_Eav_Model_Entity_Setup $this */
$this->startSetup();

$this->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'max_approval',  array(
    'type'                       => 'int',
    'label'                      => 'Approval Qty',
    'input'                      => 'text',
    'backend'                    => '',
    'required'                   => false,
    'sort_order'                 => 255,
    'used_in_product_listing'    => true,
));

$this->endSetup();