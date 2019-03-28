<?php

require_once 'abstract.php';

class FixEpaceOrderAddressRegions extends Mage_Shell_Abstract
{
    protected $regionsByName = [];

    public function run()
    {
        /** @var Mage_Sales_Model_Resource_Order_Address_Collection $collection */
        $collection = Mage::getResourceModel('sales/order_address_collection');
        $collection->addAttributeToFilter('epace_contact_id', ['notnull' => true])
            ->addAttributeToFilter('region', ['notnull' => true])
            ->addAttributeToFilter('region_id', ['null' => true])
            ->addOrder('entity_id', 'ASC')
            ->setPageSize(100);

        $i = 0;
        $size = $collection->getSize();
        $page = 1;
        $lastPage = $collection->getLastPageNumber();

        $skip = [];
        $skipChanged = false;

        do {
            if ($skipChanged) {
                $collection = Mage::getResourceModel('sales/order_address_collection');
                $collection->addAttributeToFilter('epace_contact_id', ['notnull' => true])
                    ->addAttributeToFilter('region', ['notnull' => true])
                    ->addAttributeToFilter('region_id', ['null' => true])
                    ->addAttributeToFilter('entity_id', ['nin' => $skip])
                    ->addOrder('entity_id', 'ASC')
                    ->setPageSize(100);
                $skipChanged = false;
            } else {
                $collection->clear();
            }
            /** @var Mage_Sales_Model_Order_Address $address */
            foreach ($collection as $address) {
                $region = $address->getRegion();
                $this->write(++$i . '/' . $size . ' ' . $address->getId() . ' ' . $region);

                $regionId = $this->getRegionIdByName($address->getRegion(), $address->getCountryId());
                if (!$regionId) {
                    $this->writeln(' Not found.');
                    $skip[] = $address->getId();
                    $skipChanged = true;
                } else {
                    $this->writeln(' ' . $regionId);
                    $address->setRegionId($regionId)->save();
                }
            }
        } while (++$page < $lastPage);
    }

    protected function getRegionIdByName($name, $country)
    {
        if (isset($this->regionsByName[$country][$name])) {
            return $this->regionsByName[$country][$name];
        }
        /** @var Mage_Directory_Model_Resource_Region_Collection $collection */
        $collection = Mage::getResourceModel('directory/region_collection');
        $collection->addFieldToFilter('country_id', $country)
            ->addFieldToFilter('default_name', $name);

        return $this->regionsByName[$country][$name] = $collection->getFirstItem()->getId();
    }

    protected function write($msg)
    {
        echo $msg;
    }

    protected function writeln($msg)
    {
        echo $msg . PHP_EOL;
    }
}

$shell = new FixEpaceOrderAddressRegions();
$shell->run();