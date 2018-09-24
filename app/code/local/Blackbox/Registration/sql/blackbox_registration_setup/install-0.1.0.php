<?php
/** @var Mage_Eav_Model_Entity_Setup $this */

$this->startSetup();

$this->addAttribute('customer', 'approved', array(
    'type'       => 'int',
    'label'      => 'Approved',
    'input'      => 'select',
    'source'     => 'eav/entity_attribute_source_boolean',
    'required'   => 0,
    'sort_order' => 500,
    'is_visible' => 1,
    'is_system'  => 0,
    'position'   => 500
));
/** @var Mage_Eav_Model_Attribute $attribute */
$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'approved');
$attribute->setUsedInForms([
    'adminhtml_customer'
]);
$attribute->save();

$this->addAttribute('customer', 'telephone', array(
    'type'       => 'varchar',
    'label'      => 'Telephone',
    'input'      => 'text',
    'required'   => 0,
    'sort_order' => 85,
    'is_visible' => 1,
    'is_system'  => 0,
    'position'   => 85
));

/** @var Mage_Eav_Model_Attribute $attribute */
$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'telephone');
$attribute->setUsedInForms([
    'adminhtml_customer',
    'checkout_register',
    'customer_account_create',
    'customer_account_edit',
    'adminhtml_checkout'
]);
$attribute->save();

$this->endSetup();