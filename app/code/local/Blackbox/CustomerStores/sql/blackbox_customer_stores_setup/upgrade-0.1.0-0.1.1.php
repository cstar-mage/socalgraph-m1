<?php
/** @var Mage_Customer_Model_Resource_Setup $this */

$this->startSetup();

$this->addAttribute('customer', 'default_billing_store', array(
    'type'       => 'int',
    'label'      => 'Default Billing Store',
    'input'      => 'select',
    'source'     => 'customer_stores/customer_attribute_source_storelocator',
    'required'   => 0,
    'sort_order' => 500,
    'is_visible' => 0,
    'is_system'  => 0,
    'position'   => 500
));

$this->addAttribute('customer', 'default_shipping_store', array(
    'type'       => 'int',
    'label'      => 'Default Shipping Store',
    'input'      => 'select',
    'source'     => 'customer_stores/customer_attribute_source_storelocator',
    'required'   => 0,
    'sort_order' => 500,
    'is_visible' => 0,
    'is_system'  => 0,
    'position'   => 500
));

$this->endSetup();