<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS {$resource->getTableName('onlinedesign/art')};
CREATE TABLE {$resource->getTableName('onlinedesign/art')} (
  `art_id` int(11) unsigned NOT NULL auto_increment,	
  `title` varchar(255) NOT NULL, 
  `category` varchar(255), 
  PRIMARY KEY  (`art_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

$installer->endSetup(); 