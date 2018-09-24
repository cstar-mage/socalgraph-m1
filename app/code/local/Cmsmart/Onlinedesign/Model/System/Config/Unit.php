<?php

class Cmsmart_Onlinedesign_Model_System_Config_Unit extends Varien_Object
{
    const cm	= "cm";
    const inch	= "inch";
    const mm	= "mm";

    static public function getOptionArray()
    {
        return array(
            self::cm    => Mage::helper('onlinedesign')->__('Cm'),
            self::inch   => Mage::helper('onlinedesign')->__('Inch'),
            self::mm   => Mage::helper('onlinedesign')->__('Milimet')
        );
    }
	
	public function toOptionArray()
    {	
      	$dur = array();
      	$dur[] = array('value' => self::cm, 'label' => Mage::helper('onlinedesign')->__('Cm'));
      	$dur[] = array('value' => self::inch, 'label' => Mage::helper('onlinedesign')->__('Inch'));
      	$dur[] = array('value' => self::mm, 'label' => Mage::helper('onlinedesign')->__('Milimet'));
      	
        return $dur;
    }
}