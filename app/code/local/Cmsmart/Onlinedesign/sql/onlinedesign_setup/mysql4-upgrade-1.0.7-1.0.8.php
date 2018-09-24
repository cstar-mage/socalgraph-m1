<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS {$resource->getTableName('onlinedesign/reject')};
CREATE TABLE {$resource->getTableName('onlinedesign/reject')} (
  `id` int(11) unsigned NOT NULL auto_increment,	
  `oid` varchar(255), 
  `pid` varchar(255), 
  `action` varchar(255), 
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

$installer->endSetup(); 