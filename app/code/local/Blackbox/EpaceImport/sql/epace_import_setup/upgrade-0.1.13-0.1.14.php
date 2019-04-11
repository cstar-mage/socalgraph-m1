<?php

/** @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'epacei/purchase_order_address'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('epacei/address'))
    ->addColumn('address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Entity Id')
    ->addColumn('customer_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'Customer Address Id')
    ->addColumn('region_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'Region Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'Customer Id')
    ->addColumn('sales_person_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'Sales Person Id')
    ->addColumn('epace_contact_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'Epace Contact Id')
    ->addColumn('fax', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Fax')
    ->addColumn('region', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Region')
    ->addColumn('postcode', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Postcode')
    ->addColumn('lastname', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Lastname')
    ->addColumn('street', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Street')
    ->addColumn('city', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'City')
    ->addColumn('email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Email')
    ->addColumn('telephone', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Telephone')
    ->addColumn('country_id', Varien_Db_Ddl_Table::TYPE_TEXT, 2, array(
    ), 'Country Id')
    ->addColumn('firstname', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Firstname')
    ->addColumn('prefix', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Prefix')
    ->addColumn('middlename', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Middlename')
    ->addColumn('suffix', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Suffix')
    ->addColumn('company', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Company')
    ->setComment('Epace Flat Address');
$installer->getConnection()->createTable($table);

/**
 * Create table 'epacei/vendor'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('epacei/vendor'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Entity Id')
    ->addColumn('epace_vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
    ), 'Epace Vendor Id')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'default'   => 1,
    ), 'Vendor Type')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Website Id')
    ->addColumn('email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Email')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'default'   => '0',
    ), 'Store Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
    ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
    ), 'Updated At')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
    ), 'Is Active')
    ->addColumn('dob', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Date Of Birth')
    ->addColumn('firstname', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'First Name')
    ->addColumn('gender', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'Gender')
    ->addColumn('lastname', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Last Name')
    ->addColumn('middlename', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Middle Name/Initial')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Position')
    ->addColumn('prefix', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Prefix')
    ->addColumn('provider', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'provider')
    ->addColumn('suffix', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Suffix')
    ->addColumn('taxvat', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Tax/VAT Number')
    ->addColumn('telephone', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Telephone')
    ->addColumn('shipping_method', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Shipping Method')
    ->addColumn('ship_to_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => true,
    ), 'Ship To Address Id')
    ->addColumn('payment_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => true,
    ), 'Payment Address Id')
    ->addColumn('ship_from_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => true,
    ), 'Ship From Address Id')
    ->addIndex($installer->getIdxName('epacei/vendor', array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName('epacei/vendor', array('email', 'website_id')),
        array('email', 'website_id'))
    ->addIndex($installer->getIdxName('epacei/vendor', array('website_id')),
        array('website_id'))
    ->addForeignKey($installer->getFkName('epacei/vendor', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('epacei/vendor', 'website_id', 'core/website', 'website_id'),
        'website_id', $installer->getTable('core/website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'epacei/vendor',
            'ship_to_address_id',
            'epacei/address',
            'address_id'
        ),
        'ship_to_address_id', $installer->getTable('epacei/address'), 'address_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'epacei/vendor',
            'payment_address_id',
            'epacei/address',
            'address_id'
        ),
        'payment_address_id', $installer->getTable('epacei/address'), 'address_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'epacei/vendor',
            'ship_from_address_id',
            'epacei/address',
            'address_id'
        ),
        'ship_from_address_id', $installer->getTable('epacei/address'), 'address_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Vendor');
$installer->getConnection()->createTable($table);

/**
 * Create table 'epacei/purchase_order'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('epacei/purchase_order'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Entity Id')
    ->addColumn('epace_purchase_order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Epace object id')
    ->addColumn('ship_to_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => true,
    ), 'Ship To Address Id')
    ->addColumn('vendor_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => true,
    ), 'Vendor Address Id')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_CHAR, 1, array(
    ), 'Status')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'Purchase Order Type')
    ->addColumn('shipping_description', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Shipping Description')
    ->addColumn('requester', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Requester')
    ->addColumn('is_virtual', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Is Virtual')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Store Id')
    ->addColumn('vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
    ), 'Vendor Id')
    ->addColumn('vendor_contact_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
    ), 'Vendor Id')
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
    ->addColumn('base_original_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Original Total')
    ->addColumn('base_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Tax Amount')
    ->addColumn('base_tax_amount2', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Tax Amount')
    ->addColumn('base_to_global_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base To Global Rate')
    ->addColumn('base_to_order_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base To Order Rate')
    ->addColumn('base_total_qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Base Total Qty')
    ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Discount Amount')
    ->addColumn('grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Grand Total')
    ->addColumn('store_to_base_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Store To Base Rate')
    ->addColumn('store_to_order_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Store To Order Rate')
    ->addColumn('subtotal', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Subtotal')
    ->addColumn('original_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Original Total')
    ->addColumn('tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Tax Amount')
    ->addColumn('tax_amount2', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    ), 'Tax Amount 2')
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
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
    ), 'Increment Id')
    ->addColumn('base_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
    ), 'Base Currency Code')
    ->addColumn('email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Email')
    ->addColumn('company', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Company')
    ->addColumn('contact_firstname', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Contact Firstname')
    ->addColumn('contact_lastname', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Contact Lastname')
    ->addColumn('contact_middlename', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Customer Middlename')
    ->addColumn('contact_prefix', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Customer Prefix')
    ->addColumn('contact_suffix', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Customer Suffix')
    ->addColumn('taxvat', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Taxvat')
    ->addColumn('street', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Street')
    ->addColumn('city', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'City')
    ->addColumn('region', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Region')
    ->addColumn('region_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
    ), 'Region Id')
    ->addColumn('postcode', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Postcode')
    ->addColumn('country_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Country Id')
    ->addColumn('telephone', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Telephone')
    ->addColumn('po_number', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'PO Number')
    ->addColumn('discount_description', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Discount Description')
    ->addColumn('global_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
    ), 'Global Currency Code')
    ->addColumn('hold_before_status', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Hold Before Status')
    ->addColumn('order_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
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
    ->addColumn('shipping_method', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Shipping Method')
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
    ->addIndex($installer->getIdxName('epacei/purchase_order', array('status')),
        array('status'))
    ->addIndex($installer->getIdxName('epacei/purchase_order', array('store_id')),
        array('store_id'))
    ->addIndex(
        $installer->getIdxName(
            'epacei/purchase_order',
            array('increment_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('increment_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex(
        $installer->getIdxName(
            'epacei/purchase_order',
            array('epace_purchase_order_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('epace_purchase_order_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('epacei/purchase_order', array('created_at')),
        array('created_at'))
    ->addIndex($installer->getIdxName('epacei/purchase_order', array('vendor_id')),
        array('vendor_id'))
    ->addIndex($installer->getIdxName('epacei/purchase_order', array('updated_at')),
        array('updated_at'))
    ->addForeignKey($installer->getFkName('epacei/purchase_order', 'vendor_id', 'epacei/vendor', 'entity_id'),
        'vendor_id', $installer->getTable('epacei/vendor'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('epacei/purchase_order', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'epacei/purchase_order',
            'ship_to_address_id',
            'epacei/address',
            'address_id'
        ),
        'ship_to_address_id', $installer->getTable('epacei/address'), 'address_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'epacei/purchase_order',
            'vendor_address_id',
            'epacei/address',
            'address_id'
        ),
        'vendor_address_id', $installer->getTable('epacei/address'), 'address_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Epace Flat Purchase Order');
$installer->getConnection()->createTable($table);

/**
 * Create table 'epacei/purchase_order_item'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('epacei/purchase_order_item'))
    ->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Item Id')
    ->addColumn('purchase_order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Purchase Order Id')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => true,
    ), 'Order Id')
    ->addColumn('order_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'Order Item Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Store Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
    ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
    ), 'Updated At')
    ->addColumn('order_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
    ), 'Order Item Id')
    ->addColumn('weight', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => '0.0000',
    ), 'Weight')
    ->addColumn('is_virtual', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Is Virtual')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
    ), 'Description')
    ->addColumn('applied_rule_ids', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
    ), 'Applied Rule Ids')
    ->addColumn('additional_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
    ), 'Additional Data')
    ->addColumn('is_qty_decimal', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Is Qty Decimal')
    ->addColumn('uom', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
    ), 'UOM')
    ->addColumn('qty_uom', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
    ), 'Qty UOM')
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
    ->addColumn('extended_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
    ), 'Price')
    ->addColumn('base_extended_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
    ), 'Base Price')
    ->addColumn('tax_percent', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => '0.0000',
    ), 'Tax Percent')
    ->addColumn('tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => '0.0000',
    ), 'Tax Amount')
    ->addColumn('tax_amount2', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => '0.0000',
    ), 'Tax Amount2')
    ->addColumn('base_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => '0.0000',
    ), 'Base Tax Amount')
    ->addColumn('base_tax_amount2', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => '0.0000',
    ), 'Base Tax Amount2')
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
    ->addIndex($installer->getIdxName('epacei/purchase_order_item', array('purchase_order_id')),
        array('purchase_order_id'))
    ->addIndex($installer->getIdxName('epacei/purchase_order_item', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('epacei/purchase_order_item', 'purchase_order_id', 'epacei/purchase_order', 'entity_id'),
        'purchase_order_id', $installer->getTable('epacei/purchase_order'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('epacei/purchase_order_item', 'order_id', 'sales/order', 'entity_id'),
        'order_id', $installer->getTable('sales/order'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('epacei/purchase_order_item', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Epace Flat Purchase Order Item');
$installer->getConnection()->createTable($table);

/**
 * Create table 'epacei/purchase_order_status_history'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('epacei/purchase_order_status_history'))
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
    ->addIndex($installer->getIdxName('epacei/purchase_order_status_history', array('parent_id')),
        array('parent_id'))
    ->addIndex($installer->getIdxName('epacei/purchase_order_status_history', array('created_at')),
        array('created_at'))
    ->addForeignKey($installer->getFkName('epacei/purchase_order_status_history', 'parent_id', 'epacei/purchase_order', 'entity_id'),
        'parent_id', $installer->getTable('epacei/purchase_order'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Epace Flat Purchase Order Status History');
$installer->getConnection()->createTable($table);

$installer->endSetup();
