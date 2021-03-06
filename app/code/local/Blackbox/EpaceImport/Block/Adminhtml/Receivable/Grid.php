<?php
/**
 * Adminhtml epacei receivables grid
 */
class Blackbox_EpaceImport_Block_Adminhtml_Receivable_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('epacei_receivable_grid');
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
        return 'epacei/receivable_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('real_receivable_id', array(
            'header'=> Mage::helper('epacei')->__('Receivable #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
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
            'header' => Mage::helper('epacei')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('epacei')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('epacei')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'receivable_currency_code',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('epacei')->__('Status'),
            'index' => 'state',
            'type'  => 'options',
            'width' => '70px',
            'options' => [
                Blackbox_EpaceImport_Model_Receivable::STATE_OPEN => 'Open',
                Blackbox_EpaceImport_Model_Receivable::STATE_CLOSED => 'Closed',
                Blackbox_EpaceImport_Model_Receivable::STATE_DISPUTED => 'Disputed'
            ],
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('epacei/receivable/actions/view')) {
            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('epacei')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('epacei')->__('View'),
                            'url'     => array('base'=>'*/epace_receivable/view'),
                            'field'   => 'receivable_id',
                            'data-column' => 'action',
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
                ));
        }

        $this->addExportType('*/*/exportCsv', Mage::helper('epacei')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('epacei')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('receivable_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        if (Mage::getSingleton('admin/session')->isAllowed('epacei/receivable/actions/cancel')) {
            $this->getMassactionBlock()->addItem('cancel_receivable', array(
                'label'=> Mage::helper('epacei')->__('Cancel'),
                'url'  => $this->getUrl('*/epace_receivable/massCancel'),
            ));
        }

        $this->getMassactionBlock()->addItem('pdfdocs_receivable', array(
            'label'=> Mage::helper('epacei')->__('Print All'),
            'url'  => $this->getUrl('*/epace_receivable/pdfdocs'),
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('epacei/receivable/actions/view')) {
            return $this->getUrl('*/epace_receivable/view', array('receivable_id' => $row->getId()));
        }
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

}
