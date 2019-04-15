<?php
/**
 * Adminhtml purchase orders grid
 */
class Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('epacei_purchase_order_grid');
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
        return 'epacei/purchaseOrder_collection';
    }

    protected function _prepareCollection()
    {
        /** @var Blackbox_EpaceImport_Model_Resource_PurchaseOrder_Collection $collection */
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->getSelect()->columns(['contact_name' => 'CONCAT(COALESCE(contact_firstname, \'\'), \' \', COALESCE(contact_lastname, \'\'))']);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('po_number', array(
            'header'=> Mage::helper('epacei')->__('PurchaseOrder #'),
            'width' => '10px',
            'type'  => 'text',
            'index' => 'po_number',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('epacei')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
                'escape'  => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('epacei')->__('Created At'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('company', [
            'header' => Mage::helper('epacei')->__('Company'),
            'index' => 'company',
            'width' => '70px',
        ]);

        $this->addColumn('contact_name', [
            'header' => Mage::helper('epacei')->__('Contact Name'),
            'index' => 'contact_name',
            'width' => '70px',
        ]);

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('epacei')->__('Grand Total'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('epacei')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('epacei/purchaseOrder_config')->getStatuses(),
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('epacei/purchase_order/actions/view')) {
            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('epacei')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('epacei')->__('View'),
                            'url'     => array('base'=>'*/epace_purchaseOrder/view'),
                            'field'   => 'purchase_order_id',
                            'data-column' => 'action',
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
            ));
        }

//        $this->addExportType('*/*/exportCsv', Mage::helper('epacei')->__('CSV'));
//        $this->addExportType('*/*/exportExcel', Mage::helper('epacei')->__('Excel XML'));

        return parent::_prepareColumns();
    }

//    protected function _prepareMassaction()
//    {
//        $this->setMassactionIdField('entity_id');
//        $this->getMassactionBlock()->setFormFieldName('purchase_order_ids');
//        $this->getMassactionBlock()->setUseSelectAll(false);
//
//        if (Mage::getSingleton('admin/session')->isAllowed('epacei/purchase order/actions/cancel')) {
//            $this->getMassactionBlock()->addItem('cancel_purchase order', array(
//                 'label'=> Mage::helper('epacei')->__('Cancel'),
//                 'url'  => $this->getUrl('*/epace_purchase order/massCancel'),
//            ));
//        }
//
//        $this->getMassactionBlock()->addItem('pdfdocs_purchase order', array(
//             'label'=> Mage::helper('epacei')->__('Print All'),
//             'url'  => $this->getUrl('*/epace_purchase order/pdfdocs'),
//        ));
//
//        return $this;
//    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('epacei/purchase_order/actions/view')) {
            return $this->getUrl('*/epace_purchaseOrder/view', array('purchase_order_id' => $row->getId()));
        }
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

}
