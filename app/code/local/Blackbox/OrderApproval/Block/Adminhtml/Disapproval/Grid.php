<?php

/**
 * Adminhtml sales disapprovals grid
 */
class Blackbox_OrderApproval_Block_Adminhtml_Disapproval_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_disapproval_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'order_approval/disapproval_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('order_approval')->__('Id'),
            'index'     => 'entity_id',
            'type'      => 'int',
        ));

        $this->addColumn('order_increment_id', array(
            'header'    => Mage::helper('order_approval')->__('Order #'),
            'index'     => 'order_increment_id',
            'type'      => 'text',
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('order_approval')->__('Disapproval Date'),
            'index'     => 'created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('state', array(
            'header'    => Mage::helper('sales')->__('Status'),
            'index'     => 'state',
            'type'      => 'options',
            'options'   => Mage::getModel('order_approval/disapproval')->getStates(),
        ));

        $this->addColumn('rule_id', array(
            'header'    => Mage::helper('sales')->__('Rule'),
            'index'     => 'rule_id',
            'type'      => 'options',
            'options'   => Mage::getModel('order_approval/option_source_rule')->getRules(),
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('order_approval')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('order_approval')->__('View'),
                        'url'     => array('base'=>'orderapproval/adminhtml_disapproval/view'),
                        'field'   => 'disapproval_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('order_approval')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('order_approval')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
//        $this->setMassactionIdField('entity_id');
//        $this->getMassactionBlock()->setFormFieldName('approval_ids');
//        $this->getMassactionBlock()->setUseSelectAll(false);
//
//        $this->getMassactionBlock()->addItem('pdfapprovals_order', array(
//             'label'=> Mage::helper('order_approval')->__('PDF Approvals'),
//             'url'  => $this->getUrl('*/adminhtml_approval/pdfapprovals'),
//        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        if (!Mage::getSingleton('admin/session')->isAllowed('sales/order/disapproval')) {
            return false;
        }

        return $this->getUrl('*/adminhtml_disapproval/view',
            array(
                'disapproval_id'=> $row->getId(),
            )
        );
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

}
