<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$conn = $installer->getConnection();

if (!$conn->tableColumnExists($resource->getTableName('onlinedesign/onlinedesign'), 'nbdesigner_option')) {
	$installer->run("
	ALTER TABLE {$resource->getTableName('onlinedesign/onlinedesign')} ADD `nbdesigner_option` text default NULL;
	");
}
$installer->endSetup();