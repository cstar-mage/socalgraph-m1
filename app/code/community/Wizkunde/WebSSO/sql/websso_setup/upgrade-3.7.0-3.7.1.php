<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop signature option from metadata
 */
$installer->getConnection()
    ->addColumn($installer->getTable('websso/idp'), 'name_id_format', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'length'    => 40,
        'nullable'  => false,
        'comment'   => 'Name ID Format',
        'after'     => 'name_id'
    ));

$installer->endSetup();