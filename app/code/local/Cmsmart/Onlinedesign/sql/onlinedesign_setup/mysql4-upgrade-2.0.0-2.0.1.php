<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS {$resource->getTableName('onlinedesign/color')};
CREATE TABLE {$resource->getTableName('onlinedesign/color')} (
  `color_id` int(11) unsigned NOT NULL auto_increment,	
  `color_name` text NOT NULL, 
  `hex` varchar(255) NOT NULL, 
  PRIMARY KEY  (`color_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

$installer->endSetup(); 