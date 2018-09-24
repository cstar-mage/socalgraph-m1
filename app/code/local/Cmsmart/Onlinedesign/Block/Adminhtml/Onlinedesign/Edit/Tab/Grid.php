<?php
class Cmsmart_Onlinedesign_Block_Adminhtml_Onlinedesign_Edit_Tab_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
    {
        parent::__construct();
        $this->setId('id');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
        $collection = Mage::getModel('onlinedesign/templates')->getCollection()
        				->addFieldToFilter('product_id',$this->getRequest()->getParam('id'));
		
	}
    
    protected function _addColumnFilterToCollection($column)
    {

        if ($column->getId() == 'in_product_program') {
            $productIds = $this->_getSelectedOperators();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('id', array('in'=>$productIds));
            } else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('id', array('nin'=>$productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    // collection list member 
    protected function _prepareCollection()
    {
		$collection = Mage::getModel('onlinedesign/templates')->getCollection()
						->addFieldToFilter('product_id',$this->getRequest()->getParam('id'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
		$this->addColumn('id', array(
          'header'    => Mage::helper('onlinedesign')->__('ID'),
          'align'     => 'left',
          'index'     => 'id',
		  'align'	  => 'center',
		  'width'     => '30px',
      	));
		
		$this->addColumn('image_design', array(
			'header'    => Mage::helper('onlinedesign')->__('Design'),
			'align'     =>'center',
			'width'     => '300px',
			'index'     => 'image_design',
			'sortable'  => false,
			'renderer'  => 'onlinedesign/adminhtml_renderer_imagesdesign',
			'filter'	=> false,
			'sortable'      => false,
		));

	    $this->addColumn('priority', array(
          'header'    => Mage::helper('onlinedesign')->__('Primary'),
          'align'     => 'left',
          'index'     => 'priority',
		  'align'	  => 'center',
		  'width'     => '30px',
      	));
		
     	$this->addColumn('folder', array(
          'header'    => Mage::helper('onlinedesign')->__('Folder'),
          'align'     =>'left',
          'index'     => 'folder',
      	));
		
		$this->addColumn('created_date', array(
          'header'    => Mage::helper('onlinedesign')->__('Create At'),
          'align'     =>'left',
		  'type' => 'datetime',
		  'gmtoffset' => true,
          'index'     => 'created_date',
      	));

		$this->addColumn('action', array(
			'header'    => Mage::helper('onlinedesign')->__('Action'),
			'align'     =>'center',
			'width'     => '80px',
			'index'     => 'action',
			'sortable'  => false,
			'renderer'  => 'onlinedesign/adminhtml_renderer_editdesign',
			'filter'	=> false,
			'sortable'      => false,
		));
		
        return parent::_prepareColumns();
    }

    protected function _getSelectedOperators()
    {
        $products = array_keys($this->getSelectedAddOperators());
        return $products;
    }

    public function getSelectedAddOperators()
    { 
    	$collection = Mage::getModel('onlinedesign/templates')->getCollection()
        				->addFieldToFilter('product_id',$this->getRequest()->getParam('id'));
        $products = array();
        
        foreach ($collection as $product) {
            $products[$product->getId()] = $product->getId();
        }
        return $products;
    }
}
