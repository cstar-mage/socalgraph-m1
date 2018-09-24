<?php

$installer = $this; /* @var Mage_Eav_Model_Entity_Setup $installer */

$installer->startSetup();

//$pendigStatus = Mage::getModel('sales/order_status')->load('pending');
//$pendigStatus->setLabel('Approval Waiting')->save();

$installer->endSetup();
