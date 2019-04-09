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

    public function isCustomerCSR()
    {
        /** @var Blackbox_EpaceImport_Helper_Data $helper */
        $helper = Mage::helper('epacei');
        /** @var Mage_Customer_Model_Session $session */
        $session = Mage::getSingleton('customer/session');

        return $session->getCustomerGroupId() == $helper->getCSRCustomerGroupId();
    }

    public function getEpaceUrl($path)
    {
        /** @var Blackbox_Epace_Helper_Api $api */
        $api = Mage::helper('epace/api');
        return $api->getHost() . '/epace/company:public/' . $path;
    }
}