<?php

$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$resource->getTableName('onlinedesign/onlinedesign')};
CREATE TABLE {$resource->getTableName('onlinedesign/onlinedesign')} (
  `onlinedesign_id` int(11) unsigned NOT NULL auto_increment,
  `dpi` varchar(255) NOT NULL default '',
  `product_id` varchar(255) NOT NULL default '0',
  `content_design` text NOT NULL default '',
  `status` int(6) NOT NULL default '0',
  PRIMARY KEY (`onlinedesign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


    ");

$installer->endSetup(); 