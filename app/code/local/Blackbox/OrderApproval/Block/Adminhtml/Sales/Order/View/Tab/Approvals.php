<?php

/**
 * Order Approvals grid
 *
 * @category   Blackbox
 * @package    Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Block_Adminhtml_Sales_Order_View_Tab_Approvals
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('order_approvals');
        $this->setUseAjax(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'order_approval/approval_grid_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass())
            ->addFieldToSelect('entity_id')
            ->addFieldToSelect('created_at')
            ->addFieldToSelect('order_id')
            ->addFieldToSelect('increment_id')
            ->addFieldToSelect('user_name')
            ->addFieldToSelect('rule_id')
            ->addFieldToSelect('state')
            ->setOrderFilter($this->getOrder())
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('sales')->__('Approval #'),
            'index'     => 'increment_id',
            'width'     => '120px',
        ));

        $this->addColumn('state', array(
            'header'    => Mage::helper('sales')->__('Status'),
            'index'     => 'state',
            'type'      => 'options',
            'options'   => Mage::getModel('order_approval/approval')->getStates(),
        ));

        $this->addColumn('user_name', array(
            'header' => Mage::helper('sales')->__('Approved User'),
            'index' => 'user_name',
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('sales')->__('Approval Date'),
            'index'     => 'created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('rule_id', array(
            'header'    => Mage::helper('sales')->__('Rule'),
            'index'     => 'rule_id',
            'type'      => 'options',
            'options'   => Mage::getModel('order_approval/option_source_rule')->getRules(),
        ));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('orderapproval/adminhtml_order_approval/view',
            array(
                'approval_id'=> $row->getId(),
                'order_id'  => $row->getOrderId()
            )
        );
    }

    public function getGridUrl()
    {
        return $this->getUrl('orderapproval/adminhtml_order_approval/approvals', array('_current' => true));
    }


    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('sales')->__('Approvals');
    }

    public function getTabTitle()
    {
        return Mage::helper('sales')->__('Order Approvals');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
