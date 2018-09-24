<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS {$resource->getTableName('onlinedesign/catart')};
CREATE TABLE {$resource->getTableName('onlinedesign/catart')} (
  `cat_id` int(11) unsigned NOT NULL auto_increment,	
  `title` text NOT NULL, 
  PRIMARY KEY  (`cat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

$installer->endSetup(); 