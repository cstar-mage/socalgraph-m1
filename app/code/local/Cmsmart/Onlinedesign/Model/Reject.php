<?php

class Cmsmart_Onlinedesign_Model_Reject extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('onlinedesign/reject');
    }
	
	public function _filterCollection($oid, $pid){
		$collections = $this->getCollection()
					  ->addFieldToFilter('oid', $oid)
					  ->addFieldToFilter('pid', $pid);
		return $collections;
	}
	
	public function delRecord($oid, $pid){
		$collections = $this->_filterCollection($oid, $pid);
		if(sizeof($collections)) {
			foreach($collections as $c){
				  $productdesign = $this->load($c->getId());
				  $productdesign->delete();
			}
		}
		return;
	}
	
	public function getAction($oid, $pid){
		$collections = $this->_filterCollection($oid, $pid);
		//zend_debug::dump($collections->getData());
		if(sizeof($collections)) {
			foreach($collections as $c){
				return $c->getData('action');
			}
		}
		return;
	}
}