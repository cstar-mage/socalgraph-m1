<?php
/**
 * Adminhtml epacei estimates grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_Estimate_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('epacei_estimate_grid');
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
        return 'epacei/estimate_collection';
    }

    protected function _prepareCollection()
    {
        /** @var Blackbox_EpaceImport_Model_Resource_Estimate_Collection $collection */
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->getSelect()->columns(['customer_name' => 'CONCAT(COALESCE(customer_firstname, \'\'), \' \', COALESCE(customer_lastname, \'\'))']);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('estimate_number', array(
            'header'=> Mage::helper('epacei')->__('Estimate #'),
            'width' => '10px',
            'type'  => 'text',
            'index' => 'estimate_number',
        ));

        $this->addColumn('version', array(
            'header'=> Mage::helper('epacei')->__('Version'),
            'width' => '10px',
            'type'  => 'text',
            'index' => 'version',
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

        $this->addColumn('company', [
            'header' => Mage::helper('epacei')->__('Company'),
            'index' => 'customer_name',
            'width' => '70px',
        ]);

        $this->addColumn('salesRep', [
            'header' => Mage::helper('epacei')->__('Sales Rep'),
            'index' => 'sales_person_id',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::helper('epacei')->getSalesRepsOptions()
        ]);

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('epacei')->__('Grand Total'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'estimate_currency_code',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('epacei')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('epacei/estimate_config')->getStatuses(),
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('epacei/estimate/actions/view')) {
            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('epacei')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('epacei')->__('View'),
                            'url'     => array('base'=>'*/epace_estimate/view'),
                            'field'   => 'estimate_id',
                            'data-column' => 'action',
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
            ));
        }
        $this->addRssList('rss/estimate/new', Mage::helper('epacei')->__('New Estimate RSS'));

        $this->addExportType('*/*/exportCsv', Mage::helper('epacei')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('epacei')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('estimate_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        if (Mage::getSingleton('admin/session')->isAllowed('epacei/estimate/actions/cancel')) {
            $this->getMassactionBlock()->addItem('cancel_estimate', array(
                 'label'=> Mage::helper('epacei')->__('Cancel'),
                 'url'  => $this->getUrl('*/epace_estimate/massCancel'),
            ));
        }

        $this->getMassactionBlock()->addItem('pdfdocs_estimate', array(
             'label'=> Mage::helper('epacei')->__('Print All'),
             'url'  => $this->getUrl('*/epace_estimate/pdfdocs'),
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('epacei/estimate/actions/view')) {
            return $this->getUrl('*/epace_estimate/view', array('estimate_id' => $row->getId()));
        }
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

}
