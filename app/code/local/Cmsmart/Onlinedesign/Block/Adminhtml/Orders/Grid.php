<?php

class Cmsmart_Onlinedesign_Block_Adminhtml_Orders_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
		parent::__construct();
		$this->setId('sales_order_grid');
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
        return 'sales/order_grid_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

   protected function _prepareColumns()
   {

        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));

        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));
		
		$this->addColumn('has_design', array(
			'header'    => Mage::helper('onlinedesign')->__('Has Design'),
			'align'     =>'center',
			'width'     => '120px',
			'index'     => 'has_design',
			'sortable'  => false,
			'type'      => 'options',
			'options'   => array(
				1 => 'Has Design',
				2 => 'No Design',
			),
			'renderer'  => 'onlinedesign/adminhtml_renderer_orderdesigner',
			'filter_condition_callback' => array($this, '_filterDesignCondition'),
		));

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '80px',
                    'type'      => 'action',
                    'getter'    => 'getId',
					'align'		=> 'center',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'*/*/edit'),
                            'field'   => 'order_id',
                            'data-column' => 'action',
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
            ));
        }
        // $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));

        // $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        // $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }
	
    protected function _filterDesignCondition($collection, $column) {
       if (!$value = $column->getFilter()->getValue()) {
            return;
       }
	
	   /* $model = Mage::getModel('onlinedesign/onlinedesign')
				->getCollection()
				->addFieldToFilter('status', 1)
				;
	   
	   $product_ids = array();
	   foreach ($model as $m){
		   $product_ids[] = $m->getProductId();
	   } */

	   $onlinedesign_pid_arr = array();
	   $order_increment_id= array();
	   $product = Mage::getModel('onlinedesign/onlinedesign')
					->getCollection();
	   foreach($product as $p) {
		   $onlinedesign_pid_arr[] = $p->getProductId();
	   }
		
		$orders = Mage::getModel('sales/order')->getCollection();
		foreach($orders as $o) {
			$order = Mage::getModel('sales/order')->load($o->getId());
			$items = $order->getAllVisibleItems();
			foreach($items as $item):
				if(in_array($item->getProductId(), $onlinedesign_pid_arr)){
					$order_increment_id[] = $order->getIncrementId();
					break;
				}
			endforeach;
		}
		
	   
	   if($value == 1) {
			$this->getCollection()
				->addAttributeToFilter('increment_id', array('in' => $order_increment_id))
				->load();
	   } else {
		   $this->getCollection()
				->addAttributeToFilter('increment_id', array('nin' => $order_increment_id))
				->load();
	   }
      
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}