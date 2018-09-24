<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS {$resource->getTableName('onlinedesign/catfont')};
CREATE TABLE {$resource->getTableName('onlinedesign/catfont')} (
  `cat_id` int(11) unsigned NOT NULL auto_increment,	
  `title` text NOT NULL, 
  PRIMARY KEY  (`cat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$resource->getTableName('onlinedesign/font')};
CREATE TABLE {$resource->getTableName('onlinedesign/font')} (
  `font_id` int(11) unsigned NOT NULL auto_increment,	
  `title` varchar(255) NOT NULL, 
  `category` varchar(255), 
  PRIMARY KEY  (`font_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

$installer->endSetup(); 