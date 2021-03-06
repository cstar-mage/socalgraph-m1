<?php

class Cmsmart_Onlinedesign_Block_Adminhtml_Font_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('fontGrid');
      $this->setDefaultSort('font_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('onlinedesign/font')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('font_id', array(
          'header'    => Mage::helper('onlinedesign')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'font_id',
      ));
	
      $this->addColumn('title', array(
          'header'    => Mage::helper('onlinedesign')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

	  $cates = array();
      $cates[''] = '-- Please select cateogry --';
	  $collection = Mage::getModel('onlinedesign/catfont')->getCollection();
	  foreach ($collection as $cat) {
		 	$cates[$cat->getId()] = $cat->getTitle();
	  }

        $this->addColumn('category', array(
            'header' => Mage::helper('onlinedesign')->__('Category'),
            'align' => 'center',
            'width' => '140px',
            'index' => 'category',
            'type' => 'options',
            'options' => $cates
        ));
		
	   $this->addColumn('title_preview', array(
          'header'    => Mage::helper('onlinedesign')->__('Preview'),
          'align'     =>'center',
		  'width'     => '250px',
          'index'     => 'title_preview',
		  'sortable'  => false,
		  'filter'	  => false,
		  'renderer'  => 'onlinedesign/adminhtml_renderer_font',
      ));
	  
	  /* $this->addColumn('font_type', array(
          'header'    => Mage::helper('onlinedesign')->__('Font Type'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'font_type',
          'type'      => 'options',
          'options'   => array(
              'custom' => 'Custom Font',
              'google' => 'Gooogle Font',
          ),
      )); */
	 

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