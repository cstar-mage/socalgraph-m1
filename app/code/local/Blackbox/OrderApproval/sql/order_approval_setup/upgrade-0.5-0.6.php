<?php

$installer = $this; /* @var Mage_Eav_Model_Entity_Setup $installer */

$installer->startSetup();

Mage::getModel('sales/order_status')->setData(array(
    'status' => 'waiting_customer',
    'label' => 'Waiting Customer Response',
))->save();

$installer->getConnection()->insertArray(
    $installer->getTable('sales/order_status_state'),
    array('status', 'state', 'is_default'),
    array(
        array('waiting_customer', 'holded', '0')
    )
);

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'edit_comment_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'unsgigned' => true,
    'nullable'  => true,
    'comment'   => 'Edit Comment Id'
));

$installer->getConnection()->addForeignKey($installer->getFkName('sales/order', 'edit_comment_id', 'sales/order_status_history', 'entity_id'),
    $installer->getTable('sales/order'), 'edit_comment_id', $installer->getTable('sales/order_status_history'), 'entity_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_NO_ACTION);

$installer->endSetup();