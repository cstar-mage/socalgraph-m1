<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();

$installer->getConnection()->addColumn($resource->getTableName('sales/quote_item'), 'nbdesigner_src', 'text');
$installer->getConnection()->addColumn($resource->getTableName('sales/quote_item'), 'nbdesigner_sku', 'text');
$installer->getConnection()->addColumn($resource->getTableName('sales/order_item'), 'nbdesigner_src', 'text');
$installer->getConnection()->addColumn($resource->getTableName('sales/order_item'), 'nbdesigner_sku', 'text');

$installer->endSetup();