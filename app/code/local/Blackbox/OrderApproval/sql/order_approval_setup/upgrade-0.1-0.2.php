<?php

$installer = $this; /* @var Mage_Eav_Model_Entity_Setup $installer */

/**
 * Create table 'order_approval/approval'
 */
$installer->getConnection()->dropTable($installer->getTable('order_approval/approval'));
$table = $installer->getConnection()
    ->newTable($installer->getTable('order_approval/approval'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'identity'  => true
    ), 'Entity Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Store Id')
    ->addColumn('total_qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Total Qty')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Order Id')
    ->addColumn('email_sent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Email Sent')
    ->addColumn('state', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'State')
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'approval Rule Id')
    ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'approvald by User Id')
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
    ), 'Increment Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Updated At')
    ->addIndex($installer->getIdxName('order_approval/approval', array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName('order_approval/approval', array('order_id')),
        array('order_id'))
    ->addIndex($installer->getIdxName('order_approval/approval', array('state')),
        array('state'))
    ->addIndex(
        $installer->getIdxName(
            'order_approval/approval',
            array('increment_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('increment_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('order_approval/approval', array('created_at')),
        array('created_at'))
    ->addForeignKey($installer->getFkName('order_approval/approval', 'order_id', 'sales/order', 'entity_id'),
        'order_id', $installer->getTable('sales/order'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('order_approval/approval', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('order_approval/approval', 'user_id', 'customer/entity', 'entity_id'),
        'user_id', $installer->getTable('customer/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('order_approval/approval', 'rule_id', 'order_approval/rule', 'rule_id'),
        'rule_id', $installer->getTable('order_approval/rule'), 'rule_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Order Approval');
$installer->getConnection()->createTable($table);


/**
 * Create table 'order_approval/approval_grid'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('order_approval/approval_grid'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Entity Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Store Id')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Order Id')
    ->addColumn('state', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'State')
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
    ), 'Increment Id')
    ->addColumn('order_increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
    ), 'Order Increment Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Created At')
    ->addColumn('order_created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Order Created At')
    ->addIndex($installer->getIdxName('order_approval/approval_grid', array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName('order_approval/approval_grid', array('order_id')),
        array('order_id'))
    ->addIndex($installer->getIdxName('order_approval/approval_grid', array('state')),
        array('state'))
    ->addIndex(
        $installer->getIdxName(
            'order_approval/approval_grid',
            array('increment_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('increment_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('order_approval/approval_grid', array('order_increment_id')),
        array('order_increment_id'))
    ->addIndex($installer->getIdxName('order_approval/approval_grid', array('created_at')),
        array('created_at'))
    ->addIndex($installer->getIdxName('order_approval/approval_grid', array('order_created_at')),
        array('order_created_at'))
    ->addColumn('user_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'User Name')
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
    ), 'Rule Id')
    ->addForeignKey($installer->getFkName('order_approval/approval_grid', 'entity_id', 'order_approval/approval', 'entity_id'),
        'entity_id', $installer->getTable('order_approval/approval'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('order_approval/approval_grid', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Order Approval Grid');
$installer->getConnection()->createTable($table);


/**
 * Create table 'order_approval/approval_item'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('order_approval/approval_item'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Entity Id')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Parent Id')
    ->addColumn('qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Qty')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'Product Id')
    ->addColumn('order_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'Order Item Id')
    ->addColumn('additional_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
    ), 'Additional Data')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
    ), 'Description')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Sku')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Name')
    ->addIndex($installer->getIdxName('order_approval/approval_item', array('parent_id')),
        array('parent_id'))
    ->addForeignKey($installer->getFkName('order_approval/approval_item', 'parent_id', 'order_approval/approval', 'entity_id'),
        'parent_id', $installer->getTable('order_approval/approval'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Order Approval Item');
$installer->getConnection()->createTable($table);


/**
 * Create table 'order_approval/approval_comment'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('order_approval/approval_comment'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Entity Id')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Parent Id')
    ->addColumn('is_customer_notified', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Is Customer Notified')
    ->addColumn('is_visible_on_front', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Is Visible On Front')
    ->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
    ), 'Comment')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Created At')
    ->addIndex($installer->getIdxName('order_approval/approval_comment', array('created_at')),
        array('created_at'))
    ->addIndex($installer->getIdxName('order_approval/approval_comment', array('parent_id')),
        array('parent_id'))
    ->addForeignKey($installer->getFkName('order_approval/approval_comment', 'parent_id', 'order_approval/approval', 'entity_id'),
        'parent_id', $installer->getTable('order_approval/approval'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Order Approval Comment');
$installer->getConnection()->createTable($table);

$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'qty_approved', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'scale'     => 4,
    'precision' => 12,
    'default'   => '0.0000',
    'comment'   => 'Qty Approved'
));

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'is_approved', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'default'   => '0',
    'comment'   => 'Is Approved'
));

$entity = array(
    'entity_model' => 'order_approval/approval',
    'attribute_model' => '',
    'table' => 'order_approval/approval',
    'increment_model'       => 'eav/entity_increment_numeric',
    'increment_per_store'   => true,
);

$installer->addEntityType('approval', $entity);

