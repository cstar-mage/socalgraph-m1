<?php

class Cmsmart_Onlinedesign_Model_Onlinedesign extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('onlinedesign/onlinedesign');
    }
	
	public function _getOnlineDesignByProduct($pid){
		$onlinedesignIds = $this->getCollection()
						->addFieldToFilter('product_id', $pid);
		return $onlinedesignIds;
	}
}