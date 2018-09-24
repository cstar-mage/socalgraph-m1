<?php

$this->startSetup();

$this->getConnection()->addColumn($this->getTable('sales/quote_address'), 'event_date', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
    'comment'   => 'Event Date',
    'nullable'  => true
));

$this->getConnection()->addColumn($this->getTable('sales/quote_address'), 'shipping_date', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
    'comment'   => 'Shipping Date',
    'nullable'  => true
));

$this->getConnection()->addColumn($this->getTable('sales/order'), 'event_date', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
    'comment'   => 'Event Date',
    'nullable'  => true
));

$this->getConnection()->addColumn($this->getTable('sales/order'), 'shipping_date', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
    'comment'   => 'Shipping Date',
    'nullable'  => true
));

$this->getConnection()->addColumn($this->getTable('sales/order'), 'orig_state', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 32,
    'comment'   => 'Orig State (before PREORDER)',
    'nullable'  => true
));

$this->getConnection()->addColumn($this->getTable('sales/order'), 'orig_status', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 32,
    'comment'   => 'Orig Status (before PREORDER)',
    'nullable'  => true
));

$this->getConnection()->addColumn($this->getTable('sales/order'), 'preorder_passed', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'comment'   => 'Do not set PREORDER again',
    'nullable'  => true
));

$statusTable        = $this->getTable('sales/order_status');
$statusStateTable   = $this->getTable('sales/order_status_state');
$statusLabelTable   = $this->getTable('sales/order_status_label');

$data = array(
    array('status' => 'preorder', 'label' => 'Pre-Order')
);
$this->getConnection()->insertArray($statusTable, array('status', 'label'), $data);

$data = array(
    array('status' => 'preorder', 'state' => 'new', 'is_default' => 0)
);
$this->getConnection()->insertArray($statusStateTable, array('status', 'state', 'is_default'), $data);

$this->endSetup();