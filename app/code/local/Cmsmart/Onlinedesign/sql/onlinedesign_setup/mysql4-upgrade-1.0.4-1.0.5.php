<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$conn = $installer->getConnection();

if (!$conn->tableColumnExists($resource->getTableName('onlinedesign/font'), 'alias')) {
	$installer->run("
	ALTER TABLE {$resource->getTableName('onlinedesign/font')} ADD `alias` varchar(255) default NULL;
	");
}
$installer->endSetup();