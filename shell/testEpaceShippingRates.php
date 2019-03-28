<?php

require_once 'abstract.php';

class TestEpaceShippingRates extends Mage_Shell_Abstract
{
    /**
     * @var Blackbox_EpaceImport_Helper_Data
     */
    protected $helper;

    public function __construct()
    {
        parent::__construct();
        $this->helper = Mage::helper('epacei');
    }

    public function run()
    {
        if ($this->getArg('mongo')) {
            Blackbox_Epace_Model_Epace_AbstractObject::$useMongo = true;
        }

        /** @var Mage_Sales_Model_Resource_Order_Shipment_Collection $collection */
        $collection = Mage::getResourceModel('sales/order_shipment_collection');
        $collection->addFieldToFilter('epace_shipment_id', ['notnull' => true]);

        $i = 0;
        $size = $collection->getSize();
        $collection->setPageSize(100);

        /** @var Mage_Sales_Model_Order_Shipment $shipment */
        foreach ($collection as $shipment) {
            $this->write(++$i . '/' . $size . ' ' . $shipment->getId() . ' ' . $shipment->getEpaceShipmentId());

            $order = $shipment->getOrder();

            /** @var Blackbox_Epace_Model_Epace_Job_Shipment $jobShipment */
            $jobShipment = Mage::getModel('efi/job_shipment')->load($shipment->getEpaceShipmentId());

            $shippingMethod = $this->helper->getShippingMethod($jobShipment->getShipVia());
            $this->write(' ' . $jobShipment->getShipVia()->getShipProvider()->getName() . ' - ' . $jobShipment->getShipVia()->getDescription());

            /** @var Mage_Sales_Model_Order_Address $shippingAddress */
            $shippingAddress = null;
            /** @var Mage_Sales_Model_Order_Address $address */
            foreach ($order->getAddressesCollection() as $address) {
                if ($address->isDeleted() || $address->getAddressType() != Mage_Sales_Model_Order_Address::TYPE_SHIPPING) {
                    continue;
                }
                if ($address->getEpaceContactId() == $jobShipment->getContactId()) {
                    $shippingAddress = $address;
                    break;
                }
            }

            /** @var Mage_Sales_Model_Order_Address $addressFrom */
            $addressFrom = null;
            if (!$jobShipment->getJob()->getShipToJobContact()) {
                $this->writeln(' Missing ShipToJobContact');
                continue;
            }
            $addressFromEpaceContactId = $jobShipment->getJob()->getShipToJobContact()->getContactId();
            /** @var Mage_Sales_Model_Order_Address $address */
            foreach ($order->getAddressesCollection() as $address) {
                if ($address->isDeleted() || $address->getAddressType() != Mage_Sales_Model_Order_Address::TYPE_SHIPPING) {
                    continue;
                }
                if ($address->getEpaceContactId() == $addressFromEpaceContactId) {
                    $addressFrom = $address;
                    break;
                }
            }

//            $shippingPrice = 0;
//            $shippingCost = 0;
            if ($shippingMethod->getCarrier() == 'ups') {
                if ($shippingAddress && $addressFrom) {
                    $carrier = $this->helper->getCarrier($shippingMethod->getCarrier());

                    if ($carrier instanceof Mage_Usa_Model_Shipping_Carrier_Ups) {
                        /** @var Mage_Shipping_Model_Rate_Request $request */
                        $request = Mage::getModel('shipping/rate_request');
                        $request->setOrigCountryId($addressFrom->getCountryId());
                        $request->setOrigRegionId($addressFrom->getRegionId());
                        $request->setOrigCity($addressFrom->getCity());
                        $request->setOrigPostcode($addressFrom->getPostcode());

                        $request->setLimitMethod($shippingMethod->getMethod());

                        $request->setDestCountryId($shippingAddress->getCountryId());
                        $request->setDestRegionId($shippingAddress->getRegionId());
                        $request->setDestRegionCode($shippingAddress->getRegionCode());
                        $request->setDestCity($shippingAddress->getCity());
                        $request->setDestPostcode($shippingAddress->getPostcode());
                        $request->setDestStreet($shippingAddress->getStreetFull());

                        $request->setPackageWeight($jobShipment->getWeight());
                        $request->setBaseCurrency($this->helper->getStore()->getBaseCurrencyCode());

                        $result = $carrier->collectRates($request);
                        if (!$result->getError()) {
//                            var_dump($result->getAllRates());
                            $found = false;
                            foreach ($result->getAllRates() as $rate) {
                                if ($rate->getMethod() == $shippingMethod->getMethod()) {
                                    $shippingPrice = $rate->getPrice();
                                    $shippingCost = $rate->getCost();
                                    $this->writeln(' Price: ' . $shippingPrice . '. Cost: ' . $shippingCost);
                                    $found = true;
                                    break;
                                }
                            }
                            if (!$found) {
                                $this->writeln(' Not found.');
                            }
                        } else {
                            $this->writeln(' Error: ' . $result->getRateById(0)->getErrorMessage());
                        }
                    }
                } else {
                    $missing = [];
                    if (!$shippingAddress) {
                        $missing[] = ['dest'];
                    }
                    if (!$addressFrom) {
                        $missing[] = ['orig'];
                    }
                    $this->writeln(' Address not found. (' . implode(', ', $missing) . ')');
                }
            } else {
                $this->writeln(' skip');
            }
        }
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

$shell = new TestEpaceShippingRates();
$shell->run();