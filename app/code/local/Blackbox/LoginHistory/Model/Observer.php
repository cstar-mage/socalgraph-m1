<?php

class Blackbox_LoginHistory_Model_Observer
{
    public function customerLogin($observer)
    {
        Mage::getModel('login_history/login')->addData([
            'customer_id' => $observer->getCustomer()->getId(),
            'store_id' => Mage::app()->getStore()->getId(),
            'remote_addr' => Mage::helper('core/http')->getRemoteAddr(true),
            'date' => time(),
        ])->save();
    }

    public function updateLocations()
    {
        /** @var Blackbox_LoginHistory_Model_Resource_Login_Collection $collection */
        $collection = Mage::getResourceModel('login_history/login_collection');
        $collection->addFieldToFilter('country_id', ['null' => true]);

        if (!$collection->getSize()) {
            return;
        }

        require_once Mage::getBaseDir('lib') . DS . 'MageWorx' . DS . 'GeoIP' . DS . 'geoipcity.inc';

        $gi = geoip_open(Mage::getBaseDir() . DS . 'geoip' . DS . 'GeoLiteCity.dat', 0);

        /** @var Blackbox_LoginHistory_Model_Login $login */
        foreach ($collection as $login) {
            $record = GeoIP_record_by_addr($gi, @inet_ntop($login->getRemoteAddr()));
            if ($record) {
                $login->setCountryId($record->country_code)
                    ->setCity($record->city)
                    ->save();
            }
        }
    }
}