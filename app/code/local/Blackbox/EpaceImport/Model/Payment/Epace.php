<?php

class Blackbox_EpaceImport_Model_Payment_Epace extends Mage_Payment_Model_Method_Abstract
{
    const CODE = 'epace_payment';

    protected $_code = self::CODE;

    protected $_canUseCheckout = false;
}