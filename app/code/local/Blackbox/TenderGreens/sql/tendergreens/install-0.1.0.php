<?php

$installer = $this; /* @var Mage_Eav_Model_Entity_Setup $installer */

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'accepted', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'unsgigned' => true,
    'nullable'  => true,
    'comment'   => 'Accepted'
));

$installer->endSetup();
