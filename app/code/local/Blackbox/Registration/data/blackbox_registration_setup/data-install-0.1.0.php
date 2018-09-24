<?php
/** @var Mage_Eav_Model_Entity_Setup $this */

$this->startSetup();

$customers = Mage::getResourceModel('customer/customer_collection');

foreach ($customers as $customer) {
    $customer->setApproved(1);
    $customer->getResource()->saveAttribute($customer, 'approved');
}

$this->endSetup();