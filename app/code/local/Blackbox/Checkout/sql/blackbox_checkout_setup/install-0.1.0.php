<?php

$this->startSetup();

$this->getConnection()->addColumn($this->getTable('sales/order'), 'associated_orders', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'comment'   => 'Associated Orders'
));

Mage::getModel('sales/order_status')->setData(array(
    'status' => 'metaorder',
    'label' => 'Meta Order'
))->save();

$this->endSetup();