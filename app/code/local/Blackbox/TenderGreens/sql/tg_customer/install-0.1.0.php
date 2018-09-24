<?php

/* @var Mage_Customer_Model_Entity_Setup $this */

$this->startSetup();

$this->addAttribute('customer', 'position', array(
    'type'       => 'varchar',
    'label'      => 'Position',
    'input'      => 'text',
    'required'   => 0,
    'sort_order' => 1000,
    'is_visible' => 1,
    'is_system'  => 0,
    'position'   => 1000
));
/** @var Mage_Eav_Model_Attribute $attribute */
$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'position');
$attribute->setUsedInForms([
    'adminhtml_customer'
]);
$attribute->save();

$this->addAttribute('customer', 'store_number', array(
    'type'       => 'varchar',
    'label'      => 'Store #',
    'input'      => 'text',
    'required'   => 0,
    'sort_order' => 1000,
    'is_visible' => 1,
    'is_system'  => 0,
    'position'   => 1000
));
/** @var Mage_Eav_Model_Attribute $attribute */
$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'store_number');
$attribute->setUsedInForms([
    'adminhtml_customer'
]);
$attribute->save();

$this->endSetup();
