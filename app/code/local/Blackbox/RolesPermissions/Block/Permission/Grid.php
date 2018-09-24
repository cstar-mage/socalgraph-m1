<?php

/**
 * Permission Rules Grid
 *
 * @package Blackbox_RolesPermissions
 */
class Blackbox_RolesPermissions_Block_Permission_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize grid
     * Set sort settings
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('permission_grid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Add websites to sales rules collection
     * Set collection
     *
     * @return Blackbox_RolesPermissions_Block_Permission_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Mage_rolespermissions_Model_Mysql4_Rule_Collection */
        $collection = Mage::getModel('rolespermissions/rule')
            ->getResourceCollection();
        $collection->addWebsitesToResult();
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    /**
     * Add grid columns
     *
     * @return Blackbox_RolesPermissions_Block_Permission_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('rule_id', array(
            'header'    => Mage::helper('rolespermissions')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'rule_id',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('rolespermissions')->__('Rule Name'),
            'align'     =>'left',
            'index'     => 'name',
        ));

        $this->addColumn('scope', array(
            'header'    => Mage::helper('rolespermissions')->__('Scope'),
            'align'     => 'left',
            'width'     => '150px',
            'index'     => 'scope',
            'type'      => 'options',
            'options'   => Mage::helper('rolespermissions')->getScopeOptionsArray()
        ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('rolespermissions')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                1 => 'Active',
                0 => 'Inactive',
            ),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('rule_website', array(
                'header'    => Mage::helper('rolespermissions')->__('Website'),
                'align'     =>'left',
                'index'     => 'website_ids',
                'type'      => 'options',
                'sortable'  => false,
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(),
                'width'     => 200,
            ));
        }

        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('rolespermissions')->__('Priority'),
            'align'     => 'right',
            'index'     => 'sort_order',
            'width'     => 100,
        ));

        parent::_prepareColumns();
        return $this;
    }

    /**
     * Retrieve row click URL
     *
     * @param Varien_Object $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getRuleId()));
    }

}
