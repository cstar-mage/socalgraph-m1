<?php

class Blackbox_LoginHistory_Block_Adminhtml_Customer_Edit_Tab_Login_History extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_login_history_grid');
        $this->setDefaultSort('date');
        $this->setDefaultDir('desc');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        Mage::getModel('login_history/observer')->updateLocations();

        /** @var Blackbox_LoginHistory_Model_Resource_Login_Collection $collection */
        $collection = Mage::getResourceModel('login_history/login_collection')
            ->addFieldToFilter('customer_id', Mage::registry('current_customer')->getId());

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('customer')->__('Record #'),
            'width'     => '100',
            'index'     => 'id',
        ));

        $this->addColumn('ip_address', array(
            'header'    => Mage::helper('customer')->__('IP Address'),
            'default'   => Mage::helper('customer')->__('n/a'),
            'index'     => 'remote_addr',
            'renderer'  => 'adminhtml/customer_online_grid_renderer_ip',
            'filter'    => false,
            'sort'      => true
        ));

        $this->addColumn('country_id', array(
            'header'    => Mage::helper('customer')->__('Country ID'),
            'index'     => 'country_id',
            'type'      => 'options',
            'options'   => Mage::helper('login_history')->getCustomerCountryOptions(Mage::registry('current_customer')->getId()),
        ));

        $this->addColumn('city', array(
            'header'    => Mage::helper('customer')->__('City'),
            'index'     => 'city',
            'type'      => 'options',
            'options'   => Mage::helper('login_history')->getCustomerCityOptions(Mage::registry('current_customer')->getId()),
        ));

        $this->addColumn('date', array(
            'header'    => Mage::helper('customer')->__('Date'),
            'index'     => 'date',
            'type'      => 'datetime',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('customer')->__('Store Id'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view' => true
            ));
        }

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return '#';
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/loginHistory', array('_current' => true));
    }
}