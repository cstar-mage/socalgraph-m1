<?php

/** @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('epacei/estimate'), 'estimate_number', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable'  => true,
    'comment'   => 'Estimate Number'
));
$installer->getConnection()->addIndex($installer->getTable('epacei/estimate'),
    $installer->getIdxName('epacei/estimate', 'estimate_number', Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX),
    ['estimate_number'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX);

$installer->getConnection()->addColumn($installer->getTable('epacei/estimate'), 'version', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable'  => true,
    'comment'   => 'Version'
));

$installer->endSetup();
