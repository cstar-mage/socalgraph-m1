<?php

$installer = $this; /* @var Mage_Eav_Model_Entity_Setup $installer */

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('blackbox_notification/rule'), 'copy_method', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'     => '4',
    'nullable' => false,
    'default'   => 'copy',
    'comment'   => 'Copy Method'
));

$installer->endSetup();
