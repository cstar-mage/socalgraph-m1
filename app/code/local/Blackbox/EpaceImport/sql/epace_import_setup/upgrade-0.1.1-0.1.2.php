<?php

/** @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('epacei/estimate'), 'sales_person_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable'  => true,
    'comment'   => 'Sales Person Id'
));
$installer->getConnection()->addForeignKey($installer->getFkName('epacei/estimate', 'sales_person_id', 'customer/entity', 'entity_id'),
    $installer->getTable('epacei/estimate'), 'sales_person_id', $installer->getTable('customer/entity'), 'entity_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE);

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'sales_person_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable'  => true,
    'comment'   => 'Sales Person Id'
));
$installer->getConnection()->addForeignKey($installer->getFkName('sales/order', 'sales_person_id', 'customer/entity', 'entity_id'),
    $installer->getTable('sales/order'), 'sales_person_id', $installer->getTable('customer/entity'), 'entity_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE);

$installer->getConnection()->addColumn($installer->getTable('sales/order_address'), 'sales_person_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable'  => true,
    'comment'   => 'Sales Person Id'
));
$installer->getConnection()->addForeignKey($installer->getFkName('sales/order_address', 'sales_person_id', 'customer/entity', 'entity_id'),
    $installer->getTable('sales/order_address'), 'sales_person_id', $installer->getTable('customer/entity'), 'entity_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE);

$installer->getConnection()->addColumn($installer->getTable('sales/shipment'), 'sales_person_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable'  => true,
    'comment'   => 'Sales Person Id'
));
$installer->getConnection()->addForeignKey($installer->getFkName('sales/shipment', 'sales_person_id', 'customer/entity', 'entity_id'),
    $installer->getTable('sales/shipment'), 'sales_person_id', $installer->getTable('customer/entity'), 'entity_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE);

/**
 * Create table 'epacei/receivable'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('epacei/receivable'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'identity'  => true
    ), 'Entity Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Store Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
    ), 'Customer Id')
    ->addColumn('base_grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Grand Total')
    ->addColumn('shipping_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Shipping Tax Amount')
    ->addColumn('tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Tax Amount')
    ->addColumn('base_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Tax Amount')
    ->addColumn('store_to_order_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Store To Order Rate')
    ->addColumn('base_shipping_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Shipping Tax Amount')
    ->addColumn('base_discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Discount Amount')
    ->addColumn('base_to_order_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base To Order Rate')
    ->addColumn('grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Grand Total')
    ->addColumn('shipping_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Shipping Amount')
    ->addColumn('subtotal_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Subtotal Incl Tax')
    ->addColumn('base_subtotal_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Subtotal Incl Tax')
    ->addColumn('store_to_base_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Store To Base Rate')
    ->addColumn('base_shipping_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Shipping Amount')
    ->addColumn('base_to_global_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base To Global Rate')
    ->addColumn('subtotal', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Subtotal')
    ->addColumn('base_subtotal', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Subtotal')
    ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Discount Amount')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Order Id')
    ->addColumn('invoice_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Invoice Id')
    ->addColumn('email_sent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Email Sent')
    ->addColumn('state', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'State')
    ->addColumn('store_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
    ), 'Store Currency Code')
    ->addColumn('transaction_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Transaction Id')
    ->addColumn('order_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
    ), 'Order Currency Code')
    ->addColumn('base_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
    ), 'Base Currency Code')
    ->addColumn('global_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
    ), 'Global Currency Code')
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
    ), 'Increment Id')
    ->addColumn('customer_email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Customer Email')
    ->addColumn('customer_firstname', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Customer Firstname')
    ->addColumn('customer_lastname', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Customer Lastname')
    ->addColumn('customer_middlename', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Customer Middlename')
    ->addColumn('customer_prefix', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Customer Prefix')
    ->addColumn('customer_suffix', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Customer Suffix')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Updated At')
    ->addColumn('hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Hidden Tax Amount')
    ->addColumn('base_hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Hidden Tax Amount')
    ->addColumn('shipping_hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Shipping Hidden Tax Amount')
    ->addColumn('base_shipping_hidden_tax_amnt', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Shipping Hidden Tax Amount')
    ->addColumn('shipping_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Shipping Incl Tax')
    ->addColumn('base_shipping_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Shipping Incl Tax')
    ->addColumn('base_total_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Total Refunded')
    ->addColumn('unpaid_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Unpaid Amount')
    ->addColumn('invoice_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Invoice Date')
    ->addColumn('due_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Due Date')
    ->addColumn('expected_payment_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Expected Payment Date')
    ->addColumn('date_paid_off', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Date Paid Off')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Description')
    ->addColumn('gl_register_number', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'glRegisterNumber')
    ->addColumn('epace_receivable_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Epace Receivable Id')
    ->addIndex($installer->getIdxName('epacei/receivable', array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName('epacei/receivable', array('grand_total')),
        array('grand_total'))
    ->addIndex($installer->getIdxName('epacei/receivable', array('order_id')),
        array('order_id'))
    ->addIndex($installer->getIdxName('epacei/receivable', array('state')),
        array('state'))
    ->addIndex(
        $installer->getIdxName(
            'epacei/receivable',
            array('increment_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('increment_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('epacei/receivable', array('created_at')),
        array('created_at'))
    ->addForeignKey($installer->getFkName('epacei/receivable', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id', $installer->getTable('customer/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('epacei/receivable', 'order_id', 'sales/order', 'entity_id'),
        'order_id', $installer->getTable('sales/order'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('epacei/receivable', 'invoice_id', 'sales/invoice', 'entity_id'),
        'invoice_id', $installer->getTable('sales/invoice'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('epacei/receivable', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Sales Flat Invoice');
$installer->getConnection()->createTable($table);

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'job_value', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'scale'     => 4,
    'precision' => 12,
    'nullable'  => true,
    'comment'   => 'Job Value'
));

$installer->endSetup();