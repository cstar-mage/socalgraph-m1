<?php

class Cmsmart_Onlinedesign_Block_Adminhtml_Googlefont_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('fontGrid');
      $this->setDefaultSort('font_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  public function getGoogleFontData(){
	  $helper = Mage::helper('onlinedesign/data');
	  $path = $helper->plugin_path_data(). DS . 'googlefonts.json';
	  $list = $helper->nbdesigner_read_json_setting($path);
	  return $list;
  }
  
  protected function _prepareCollection()
  {  
	  $collection = new Varien_Data_Collection(); 
	  $list = $this->getGoogleFontData();
	
	  foreach ($list as $row) {
			$rowObj = new Varien_Object();
			$rowObj->setData($row);
			$collection->addItem($rowObj);
	  }           

	  $this->setCollection($collection);
	  return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('id', array(
          'header'    => Mage::helper('onlinedesign')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'id',
		  'filter_condition_callback' => array($this, '_filterIDCondition'),
      ));

      $this->addColumn('name', array(
          'header'    => Mage::helper('onlinedesign')->__('Title'),
          'align'     =>'left',
          'index'     => 'name',
		  'filter_condition_callback' => array($this, '_filterNameCondition'),
      ));
	  
	  $this->addColumn('name_preview', array(
          'header'    => Mage::helper('onlinedesign')->__('Preview'),
          'align'     =>'center',
		  'width'     => '250px',
          'index'     => 'name_preview',
		  'sortable'  => false,
		  'filter'	  => false,
		  'renderer'  => 'onlinedesign/adminhtml_renderer_gfont',
      ));
	  
	  //$this->addExportType('*/*/exportCsv', Mage::helper('onlinedesign')->__('CSV'));
	  //$this->addExportType('*/*/exportXml', Mage::helper('onlinedesign')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _filterNameCondition($collection, $column)
    {
       if (!$value = $column->getFilter()->getValue()) {
            return;
        }

	  $collection = new Varien_Data_Collection(); 
	  $list = $this->getGoogleFontData();

	  foreach ($list as $row) {
		  if($row['name'] == $value) {
			$rowObj = new Varien_Object();
			$rowObj->setData($row);
			$collection->addItem($rowObj);
		  }
	  }           
	  $this->setCollection($collection);
    }
	
	protected function _filterIDCondition($collection, $column)
    {
       if (!$value = $column->getFilter()->getValue()) {
            return;
        }

	  $collection = new Varien_Data_Collection(); 
	  $list = $this->getGoogleFontData();

	  foreach ($list as $row) {
		  if($row['id'] == $value) {
			$rowObj = new Varien_Object();
			$rowObj->setData($row);
			$collection->addItem($rowObj);
		  }
	  }           
	  $this->setCollection($collection);
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
        return $this;
    }

   public function getRowUrl($row)
   {
		return "https://fonts.google.com/specimen/".$row->getName();
   }

}