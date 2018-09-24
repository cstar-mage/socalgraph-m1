<?php

class Cmsmart_Onlinedesign_Block_Adminhtml_Art_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('artGrid');
      $this->setDefaultSort('art_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('onlinedesign/art')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('art_id', array(
          'header'    => Mage::helper('onlinedesign')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'art_id',
      ));
	  
	  $this->addColumn('filename', array(
          'header'    => Mage::helper('onlinedesign')->__('Image'),
          'align'     =>'center',
		  'width'     => '120px',
          'index'     => 'filename',
		  'sortable'  => false,
		  'filter'	  => false,
		  'renderer'  => 'onlinedesign/adminhtml_renderer_art',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('onlinedesign')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

	  $this->addColumn('category', array(
          'header'    => Mage::helper('onlinedesign')->__('Category'),
          'align'     =>'left',
          'index'     => 'category',
      ));
	  
	  $cates = array();
      $cates[''] = '-- Please select cateogry --';
	  $collection = Mage::getModel('onlinedesign/catart')->getCollection();
	  foreach ($collection as $cat) {
		 	$cates[$cat->getId()] = $cat->getTitle();
	  }

        $this->addColumn('category', array(
            'header' => Mage::helper('onlinedesign')->__('Category'),
            'align' => 'center',
            'width' => '250px',
            'index' => 'category',
            'type' => 'options',
            'options' => $cates
        ));

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('onlinedesign')->__('Action'),
                'width'     => '150',
                'type'      => 'action',
				'align'		=> 'center',
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

        // $statuses = Mage::getSingleton('productdesign/status')->getOptionArray();

        // array_unshift($statuses, array('label'=>'', 'value'=>''));
        // $this->getMassactionBlock()->addItem('status', array(
             // 'label'=> Mage::helper('productdesign')->__('Change status'),
             // 'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             // 'additional' => array(
                    // 'visibility' => array(
                         // 'name' => 'status',
                         // 'type' => 'select',
                         // 'class' => 'required-entry',
                         // 'label' => Mage::helper('productdesign')->__('Status'),
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