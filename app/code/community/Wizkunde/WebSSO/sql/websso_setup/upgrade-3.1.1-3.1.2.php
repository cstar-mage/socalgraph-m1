<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**alter
 * Create table 'wizkunde_websso_claim'
 */
$installer->getConnection()
    ->addColumn($installer->getTable('websso/idp'), 'sign_metadata', array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'    => 255,
            'nullable'  => false,
            'comment'   => 'Sign SP Metadata')
    );

$installer->endSetup();