<?php
/** @var Mage_Customer_Model_Resource_Setup $this */

$this->startSetup();

$this->addAttribute('customer', 'visible_stores', array(
    'type'       => 'text',
    'label'      => 'Visible Stores',
    'input'      => 'multiselect',
    'source'     => 'customer_stores/customer_attribute_source_storelocator',
    'required'   => 0,
    'sort_order' => 500,
    'is_visible' => 1,
    'is_system'  => 0,
    'position'   => 500
));
$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'visible_stores');
$attribute->setUsedInForms([
    'adminhtml_customer'
]);
$attribute->save();

$this->endSetup();