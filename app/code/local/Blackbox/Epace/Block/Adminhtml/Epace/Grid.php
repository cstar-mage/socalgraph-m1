<?php
class Blackbox_Epace_Block_Adminhtml_Epace_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('epaceGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('epace/event')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {
        $this->addColumn('id',
            array(
                'header' => Mage::helper('epace')->__('Id'),
                'align' =>'left',
                'index' => 'id',
                'width'     => '50px',
                'type' => 'number'
            ));
        $this->addColumn('name',
            array(
                'header' => Mage::helper('epace')->__('Name'),
                'align' =>'left',
                'index' => 'name',
                'type' => 'options',
                'options' => Mage::getModel('epace/event_source_name')->toArray(),
                'filter_condition_callback' => array($this, '_addSelectFilterToCollection'),
            ));
        $this->addColumn('processed_time',
            array(
                'header' => Mage::helper('epace')->__('Processed time'),
                'align' =>'left',
                'index' => 'processed_time',
                'type' => 'date'
            ));
        $this->addColumn('status',
            array(
                'header' => Mage::helper('epace')->__('Status'),
                'align' =>'left',
                'index' => 'status',
                'type' => 'options',
                'options' => array('Success', 'With errors', 'Critical'),
                'filter_condition_callback' => array($this, '_addSelectFilterToCollection'),
            ));
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('epace')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('View'),
                        'url'     => array(
                            'base'=>'*/*/view'
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
            ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array('id' => $row->getId()));
    }

    protected function _addSelectFilterToCollection($collection, $column)
    {
        $field = ( $column->getFilterIndex() ) ? $column->getFilterIndex() : $column->getIndex();

        $value = $column->getFilter()->getValue();
        $cond = $column->getOptions()[$value];

        if ($field && isset($cond)) {
            $collection->addFieldToFilter($field , $cond);
        }

        return $this;
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->setUseSelectAll(false);
        $this->getMassactionBlock()->addItem('delete_events', array(
            'label'=> Mage::helper('sales')->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete'),
        ));
        $this->getMassactionBlock()->addItem('resend_events', array(
            'label'=> Mage::helper('sales')->__('Resend'),
            'url'  => $this->getUrl('*/*/massResend'),
        ));

        return $this;
    }
}