<?php

class Blackbox_CinemaCloud_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isCustomerSalesRep()
    {
        /** @var Blackbox_EpaceImport_Helper_Data $helper */
        $helper = Mage::helper('epacei');
        /** @var Mage_Customer_Model_Session $session */
        $session = Mage::getSingleton('customer/session');

        return $session->getCustomerGroupId() == $helper->getWholesaleCustomerGroupId();
    }
}