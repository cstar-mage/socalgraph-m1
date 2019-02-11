<?php

/** @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'epacei/estimate'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('epacei/estimate'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Entity Id')
    ->addColumn('epace_estimate_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Epace object id')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'Status')
    ->addColumn('is_virtual', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Is Virtual')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Store Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
    ), 'Customer Id')
    ->addColumn('base_discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Discount Amount')
    ->addColumn('base_discount_canceled', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Discount Canceled')
    ->addColumn('base_grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Grand Total')
    ->addColumn('base_shipping_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Shipping Tax Amount')
    ->addColumn('base_subtotal', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Subtotal')
    ->addColumn('base_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Tax Amount')
    ->addColumn('base_to_global_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base To Global Rate')
    ->addColumn('base_to_estimate_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base To Order Rate')
    ->addColumn('base_total_qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Total Qty')
    ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Discount Amount')
    ->addColumn('grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Grand Total')
    ->addColumn('store_to_base_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Store To Base Rate')
    ->addColumn('store_to_estimate_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Store To Order Rate')
    ->addColumn('subtotal', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Subtotal')
    ->addColumn('tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Tax Amount')
    ->addColumn('total_qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Total Qty')
    ->addColumn('customer_is_guest', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Customer Is Guest')
    ->addColumn('customer_note_notify', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Customer Note Notify')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
    ), 'Customer Group Id')
    ->addColumn('edit_increment', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'Edit Increment')
    ->addColumn('email_sent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Email Sent')
    ->addColumn('quote_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'Quote Address Id')
    ->addColumn('quote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'Quote Id')
    ->addColumn('adjustment_negative', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Adjustment Negative')
    ->addColumn('adjustment_positive', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Adjustment Positive')
    ->addColumn('base_adjustment_negative', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Adjustment Negative')
    ->addColumn('base_adjustment_positive', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Adjustment Positive')
    ->addColumn('base_subtotal_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Subtotal Incl Tax')
    ->addColumn('base_total_due', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Total Due')
    ->addColumn('subtotal_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Subtotal Incl Tax')
    ->addColumn('total_due', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Total Due')
    ->addColumn('weight', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Weight')
    ->addColumn('customer_dob', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    ), 'Customer Dob')
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
    ), 'Increment Id')
    ->addColumn('base_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
    ), 'Base Currency Code')
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
    ->addColumn('customer_taxvat', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Customer Taxvat')
    ->addColumn('discount_description', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Discount Description')
    ->addColumn('ext_customer_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Ext Customer Id')
    ->addColumn('ext_estimate_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Ext Order Id')
    ->addColumn('global_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
    ), 'Global Currency Code')
    ->addColumn('hold_before_status', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Hold Before Status')
    ->addColumn('estimate_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Order Currency Code')
    ->addColumn('original_increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
    ), 'Original Increment Id')
    ->addColumn('relation_child_id', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
    ), 'Relation Child Id')
    ->addColumn('relation_child_real_id', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
    ), 'Relation Child Real Id')
    ->addColumn('relation_parent_id', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
    ), 'Relation Parent Id')
    ->addColumn('relation_parent_real_id', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
    ), 'Relation Parent Real Id')
    ->addColumn('remote_ip', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Remote Ip')
    ->addColumn('store_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
    ), 'Store Currency Code')
    ->addColumn('store_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Store Name')
    ->addColumn('x_forwarded_for', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'X Forwarded For')
    ->addColumn('customer_note', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
    ), 'Customer Note')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Updated At')
    ->addColumn('total_item_count', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Total Item Count')
    ->addColumn('customer_gender', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'Customer Gender')
    ->addColumn('hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Hidden Tax Amount')
    ->addColumn('base_hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Hidden Tax Amount')
    ->addIndex($installer->getIdxName('epacei/estimate', array('status')),
        array('status'))
    ->addIndex($installer->getIdxName('epacei/estimate', array('store_id')),
        array('store_id'))
    ->addIndex(
        $installer->getIdxName(
            'epacei/estimate',
            array('increment_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('increment_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex(
        $installer->getIdxName(
            'epacei/estimate',
            array('epace_estimate_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('epace_estimate_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('epacei/estimate', array('created_at')),
        array('created_at'))
    ->addIndex($installer->getIdxName('epacei/estimate', array('customer_id')),
        array('customer_id'))
    ->addIndex($installer->getIdxName('epacei/estimate', array('ext_estimate_id')),
        array('ext_estimate_id'))
    ->addIndex($installer->getIdxName('epacei/estimate', array('quote_id')),
        array('quote_id'))
    ->addIndex($installer->getIdxName('epacei/estimate', array('updated_at')),
        array('updated_at'))
    ->addForeignKey($installer->getFkName('epacei/estimate', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id', $installer->getTable('customer/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('epacei/estimate', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Epace Flat Estimate');
$installer->getConnection()->createTable($table);

/**
 * Create table 'epacei/estimate_grid'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('epacei/estimate_grid'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Entity Id')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
    ), 'Status')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Store Id')
    ->addColumn('store_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Store Name')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
    ), 'Customer Id')
    ->addColumn('base_grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Grand Total')
    ->addColumn('base_total_paid', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Total Paid')
    ->addColumn('grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Grand Total')
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
    ), 'Increment Id')
    ->addColumn('base_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
    ), 'Base Currency Code')
    ->addColumn('estimate_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Order Currency Code')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Updated At')
    ->addIndex($installer->getIdxName('epacei/estimate_grid', array('status')),
        array('status'))
    ->addIndex($installer->getIdxName('epacei/estimate_grid', array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName('epacei/estimate_grid', array('base_grand_total')),
        array('base_grand_total'))
    ->addIndex($installer->getIdxName('epacei/estimate_grid', array('base_total_paid')),
        array('base_total_paid'))
    ->addIndex($installer->getIdxName('epacei/estimate_grid', array('grand_total')),
        array('grand_total'))
    ->addIndex(
        $installer->getIdxName(
            'epacei/estimate_grid',
            array('increment_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('increment_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('epacei/estimate_grid', array('created_at')),
        array('created_at'))
    ->addIndex($installer->getIdxName('epacei/estimate_grid', array('customer_id')),
        array('customer_id'))
    ->addIndex($installer->getIdxName('epacei/estimate_grid', array('updated_at')),
        array('updated_at'))
    ->addForeignKey($installer->getFkName('epacei/estimate_grid', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id', $installer->getTable('customer/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('epacei/estimate_grid', 'entity_id', 'epacei/estimate', 'entity_id'),
        'entity_id', $installer->getTable('epacei/estimate'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('epacei/estimate_grid', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Epace Flat Estimate Grid');
$installer->getConnection()->createTable($table);

/**
 * Create table 'epacei/estimate_status_history'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('epacei/estimate_status_history'))
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
    ->addColumn('is_customer_notified', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'Is Customer Notified')
    ->addColumn('is_visible_on_front', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Is Visible On Front')
    ->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
    ), 'Comment')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
    ), 'Status')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Created At')
    ->addIndex($installer->getIdxName('epacei/estimate_status_history', array('parent_id')),
        array('parent_id'))
    ->addIndex($installer->getIdxName('epacei/estimate_status_history', array('created_at')),
        array('created_at'))
    ->addForeignKey($installer->getFkName('epacei/estimate_status_history', 'parent_id', 'epacei/estimate', 'entity_id'),
        'parent_id', $installer->getTable('epacei/estimate'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Epace Flat Estimate Status History');
$installer->getConnection()->createTable($table);


/**
 * Create table 'epacei/estimate_item'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('epacei/estimate_item'))
    ->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Item Id')
    ->addColumn('estimate_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Estimate Id')
    ->addColumn('parent_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
    ), 'Parent Item Id')
    ->addColumn('quote_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
    ), 'Quote Item Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Store Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
    ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
    ), 'Updated At')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
    ), 'Product Id')
    ->addColumn('product_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Product Type')
    ->addColumn('product_options', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
    ), 'Product Options')
    ->addColumn('weight', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => '0.0000',
    ), 'Weight')
    ->addColumn('is_virtual', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Is Virtual')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Sku')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Name')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
    ), 'Description')
    ->addColumn('applied_rule_ids', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
    ), 'Applied Rule Ids')
    ->addColumn('additional_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
    ), 'Additional Data')
    ->addColumn('is_qty_decimal', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Is Qty Decimal')
    ->addColumn('no_discount', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'No Discount')
    ->addColumn('qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => '0.0000',
    ), 'Qty')
    ->addColumn('base_cost', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => '0.0000',
    ), 'Base Cost')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
    ), 'Price')
    ->addColumn('base_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
    ), 'Base Price')
    ->addColumn('original_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Original Price')
    ->addColumn('base_original_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Original Price')
    ->addColumn('tax_percent', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => '0.0000',
    ), 'Tax Percent')
    ->addColumn('tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => '0.0000',
    ), 'Tax Amount')
    ->addColumn('base_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => '0.0000',
    ), 'Base Tax Amount')
    ->addColumn('discount_percent', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => '0.0000',
    ), 'Discount Percent')
    ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => '0.0000',
    ), 'Discount Amount')
    ->addColumn('base_discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => '0.0000',
    ), 'Base Discount Amount')
    ->addColumn('row_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
    ), 'Row Total')
    ->addColumn('base_row_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
    ), 'Base Row Total')
    ->addColumn('row_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
    ), 'Row Invoiced')
    ->addColumn('base_row_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
    ), 'Base Row Invoiced')
    ->addColumn('row_weight', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => '0.0000',
    ), 'Row Weight')
    ->addColumn('base_tax_before_discount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Tax Before Discount')
    ->addColumn('tax_before_discount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Tax Before Discount')
    ->addColumn('ext_estimate_item_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Ext Order Item Id')
    ->addColumn('price_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Price Incl Tax')
    ->addColumn('base_price_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Price Incl Tax')
    ->addColumn('row_total_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Row Total Incl Tax')
    ->addColumn('base_row_total_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Row Total Incl Tax')
    ->addColumn('hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Hidden Tax Amount')
    ->addColumn('base_hidden_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Hidden Tax Amount')
    ->addColumn('is_nominal', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
    ), 'Is Nominal')
    ->addIndex($installer->getIdxName('epacei/estimate_item', array('estimate_id')),
        array('estimate_id'))
    ->addIndex($installer->getIdxName('epacei/estimate_item', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('epacei/estimate_item', 'estimate_id', 'epacei/estimate', 'entity_id'),
        'estimate_id', $installer->getTable('epacei/estimate'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('epacei/estimate_item', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Epace Flat Estimate Item');
$installer->getConnection()->createTable($table);

/**
 * Install eav entity types to the eav/entity_type table
 */
$installer->addEntityType('estimate', array(
    'entity_model'          => 'epacei/estimate',
    'table'                 => 'epacei/estimate',
    'increment_model'       => 'eav/entity_increment_numeric',
    'increment_per_store'   => true
));

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'estimate_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'unsgigned' => true,
    'nullable'  => true,
    'comment'   => 'Estimate Id'
));
$installer->getConnection()->addForeignKey($installer->getFkName('sales/order', 'estimate_id', 'epacei/estimate', 'entity_id'),
    $installer->getTable('sales/order'), 'estimate_id', $installer->getTable('epacei/estimate'), 'entity_id', Varien_Db_Adapter_Interface::FK_ACTION_SET_NULL);

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'epace_job_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 255,
    'nullable'  => true,
    'comment'   => 'Job Id'
));
$installer->getConnection()->addIndex($installer->getTable('sales/order'),
    $installer->getIdxName('sales/order', 'epace_job_id', Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
    ['epace_job_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE);

$installer->endSetup();
