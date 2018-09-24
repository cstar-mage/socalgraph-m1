<?php

class Cmsmart_Onlinedesign_Block_Adminhtml_Catfont_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('catGrid');
      $this->setDefaultSort('cat_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('onlinedesign/catfont')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('cat_id', array(
          'header'    => Mage::helper('onlinedesign')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'cat_id',
      ));
	  

      $this->addColumn('title', array(
          'header'    => Mage::helper('onlinedesign')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('onlinedesign')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('onlinedesign')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('onlinedesign')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('onlinedesign')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('onlinedesign_id');
        $this->getMassactionBlock()->setFormFieldName('onlinedesign');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('onlinedesign')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('onlinedesign')->__('Are you sure?')
        ));

        // $statuses = Mage::getSingleton('onlinedesign/status')->getOptionArray();

        // array_unshift($statuses, array('label'=>'', 'value'=>''));
        // $this->getMassactionBlock()->addItem('status', array(
             // 'label'=> Mage::helper('onlinedesign')->__('Change status'),
             // 'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             // 'additional' => array(
                    // 'visibility' => array(
                         // 'name' => 'status',
                         // 'type' => 'select',
                         // 'class' => 'required-entry',
                         // 'label' => Mage::helper('onlinedesign')->__('Status'),
                         // 'values' => $statuses
                     // )
             // )
        // ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}