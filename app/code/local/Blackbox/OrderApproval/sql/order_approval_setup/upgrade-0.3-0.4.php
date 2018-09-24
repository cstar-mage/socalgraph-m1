<?php

$installer = $this; /* @var Mage_Eav_Model_Entity_Setup $installer */

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'approve_info_serialized', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'     => '2M',
    'nullable' => true,
    'default'   => null,
    'comment'   => 'Qty Approved By Rules Serialized'
));

$installer->endSetup();
