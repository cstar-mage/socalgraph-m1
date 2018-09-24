<?php
class Cmsmart_Onlinedesign_Block_Mydesign extends Mage_Core_Block_Template
{
	public function __construct()
    {
        parent::__construct();
		$helper = Mage::helper('onlinedesign/data');
		$uid = $helper->get_current_user_id();      
        $collection = Mage::getModel('sales/order')->getCollection()
					->addFieldToFilter('customer_id', $uid)
					->setOrder('created_at', 'DESC')
					;
        $this->filterOrderCollectionByDesign($collection);
        $this->setCollection($collection);
    }
	
	public function _prepareLayout()
    {
		parent::_prepareLayout();
		
		$pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
        $pager->setAvailableLimit(array(5=>5,10=>10,20=>20,'all'=>'all'));
        $pager->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
    }
    
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function addItemDesignParamToPath($item, $path)
    {
        if ($sid = $this->getItemDesignSession($item)) {
            if (($pos = strpos($path, '?')) !== false && $pos != strlen($path) - 1) {
                return $path . '&nb_session=' . $sid;
            } else {
                return $path . '?nb_session=' . $sid;
            }
        }
        return $path;
    }

    protected function getItemDesignSession($item)
    {
        if ($item->getNbdesignerSession()) {
            $sid = $item->getNbdesignerSession();
        } else if($json = $item->getNbdesignerJson()){
            $json = json_decode($json, true);
            if ($json[0]) {
                if (preg_match('/^.*(?:\\\\|\\/)nbdesigner(?:\\\\|\\/)designs(?:\\\\|\\/)([^\\\\\\/]+)(?:\\\\|\\/)nb_order(?:\\\\|\\/)(?:[0-9]+)(?:\\\\|\\/).*$/', $json[0], $match)) {
                    $sid = $match[1];
                }
            }
        }
        if (!isset($sid)) {
            return '';
        }
        return $sid;
    }

    /**
     * @param Mage_Sales_Model_Resource_Order_Collection $collection
     */
    protected function filterOrderCollectionByDesign($collection)
    {
        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $select = $readConnection->select()->from($resource->getTableName('sales/order_item'), 'order_id')
            ->where('nbdesigner_json <> \'\'')
            ->group('order_id');
        $collection->getSelect()->joinInner($select, 'entity_id = order_id', '');
    }
}