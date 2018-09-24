<?php

/**
 * Notifications Log Grid
 *
 * @package Blackbox_Notification
 */
class Blackbox_Notification_Block_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize grid
     * Set sort settings
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('blackbox_notification_grid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Add websites to sales rules collection
     * Set collection
     *
     * @return Blackbox_Notification_Block_Log_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Blackbox_Notification_Model_Resource_Log_Collection */
        $collection = Mage::getModel('blackbox_notification/log')
            ->getResourceCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    /**
     * Add grid columns
     *
     * @return Blackbox_Notification_Block_Rule_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('notification_id', array(
            'header'    => Mage::helper('blackbox_notification')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'notification_id',
        ));

        $this->addColumn('type', array(
            'header'    => Mage::helper('blackbox_notification')->__('Type'),
            'width'     => '80px',
            'index'     => 'type',
            'type'      => 'options',
            'options'   => Mage::getModel('blackbox_notification/rule')->getTypes(),
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('blackbox_notification')->__('Created At'),
            'index'     => 'created_at',
            'align'     => 'right',
            'type'      => 'datetime',
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
        return $this->getUrl('*/*/view', array('id' => $row->getId()));
    }

}
