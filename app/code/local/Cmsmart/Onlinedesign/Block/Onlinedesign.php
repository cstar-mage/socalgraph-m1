<?php
class Cmsmart_Onlinedesign_Block_Onlinedesign extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getOnlinedesign()     
     { 
        if (!$this->hasData('onlinedesign')) {
            $this->setData('onlinedesign', Mage::registry('onlinedesign'));
        }
        return $this->getData('onlinedesign');
        
    }
}