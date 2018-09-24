<?php

$this->startSetup();

$this->getConnection()->addColumn($this->getTable('sales/quote_address'), 'storelocator_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'comment'   => 'Storelocator Id',
    'nullable'  => true
));

$this->getConnection()->addColumn($this->getTable('sales/quote_address'), 'storelocator_name', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 255,
    'comment'   => 'Storelocator Name',
    'nullable'  => true
));

$this->getConnection()->addColumn($this->getTable('sales/order_address'), 'storelocator_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'comment'   => 'Storelocator Id',
    'nullable'  => true
));

$this->getConnection()->addColumn($this->getTable('sales/order_address'), 'storelocator_name', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 255,
    'comment'   => 'Storelocator Name',
    'nullable'  => true
));

$this->endSetup();