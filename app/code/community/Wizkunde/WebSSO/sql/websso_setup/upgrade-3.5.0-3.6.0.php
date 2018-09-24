<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop signature option from metadata
 */
$installer->getConnection()->dropColumn($installer->getTable('websso/idp'), 'sign_metadata');

$installer->endSetup();
