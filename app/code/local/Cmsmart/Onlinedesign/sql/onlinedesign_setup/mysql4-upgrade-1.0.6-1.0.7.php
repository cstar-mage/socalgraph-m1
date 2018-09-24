<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();

$installer->getConnection()->addColumn($resource->getTableName('sales/quote_item'), 'nbdesigner_json', 'text');
$installer->getConnection()->addColumn($resource->getTableName('sales/quote_item'), 'nbdesigner_pid', 'text');
$installer->getConnection()->addColumn($resource->getTableName('sales/quote_item'), 'nbdesigner_session', 'text');
$installer->getConnection()->addColumn($resource->getTableName('sales/order_item'), 'nbdesigner_json', 'text');
$installer->getConnection()->addColumn($resource->getTableName('sales/order_item'), 'nbdesigner_pid', 'text');
$installer->getConnection()->addColumn($resource->getTableName('sales/order_item'), 'nbdesigner_session', 'text');

$installer->endSetup();