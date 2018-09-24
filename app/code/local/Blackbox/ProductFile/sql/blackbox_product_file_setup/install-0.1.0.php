<?php
/** @var Mage_Eav_Model_Entity_Setup $this */

$this->startSetup();

$this->getConnection()->changeColumn($this->getTable('downloadable/link_purchased'), 'order_item_id', 'order_item_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'unsigned'  => true,
    'nullable'  => true,
    'default'   => '0',
    'comment'   => 'Order Item ID'
), true);

$this->addAttribute('catalog_product', 'file', array(
    'type'              => 'varchar',
    'backend'           => 'jvs_fileattribute/attribute_backend_file',
    'frontend'          => '',
    'label'             => 'File',
    'input'             => 'jvs_file',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          => '',
    'is_configurable'   => false
));

$this->endSetup();