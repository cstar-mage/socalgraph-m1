<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS {$resource->getTableName('onlinedesign/templates')};
CREATE TABLE {$resource->getTableName('onlinedesign/templates')} (
  `id` int(11) unsigned NOT NULL auto_increment,	
  `product_id` varchar(255) NOT NULL, 
  `folder` varchar(255), 
  `user_id` varchar(255), 
  `created_date` datetime, 
  `publish` varchar(255), 
  `private` varchar(255), 
  `priority` varchar(255), 
  `hit` varchar(255), 
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

$installer->endSetup(); 