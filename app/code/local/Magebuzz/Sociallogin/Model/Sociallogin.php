<?php
/*
 * @copyright   Copyright (c) 2015 www.magebuzz.com
 */
class Magebuzz_Sociallogin_Model_Sociallogin extends Mage_Core_Model_Abstract
{
  public function _construct()
  {
    parent::_construct();
    $this->_init('sociallogin/sociallogin');
  }
}