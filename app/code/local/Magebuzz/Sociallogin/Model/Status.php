<?php
/*
 * @copyright   Copyright (c) 2015 www.magebuzz.com
 */
class Magebuzz_Sociallogin_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('sociallogin')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('sociallogin')->__('Disabled')
        );
    }
}