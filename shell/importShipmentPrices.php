<?php

require_once 'abstract.php';

class Blackbox_ImportShipmentPrices extends Mage_Shell_Abstract
{
    /**
     * @var Blackbox_EpaceImport_Helper_Data
     */
    protected $helper;

    /**
     * @var Mage_Core_Model_Resource
     */
    protected $resource;

    /**
     * @var Varien_Db_Adapter_Interface
     */
    protected $connection;

    protected $tabs = 0;
    protected $newLine = true;

    public function __construct()
    {
        parent::__construct();
        $this->helper = Mage::helper('epacei');
        $this->resource = Mage::getSingleton('core/resource');
        $this->connection = $this->resource->getConnection('core_read');
    }

    public function run()
    {
        if ($this->getArg('mongo')) {
            Blackbox_Epace_Model_Epace_AbstractObject::$useMongo = true;
        }

        $this->createTemporaryTable();

        $orderIdsSelect = $this->connection->select()->from($this->resource->getTableName('sales/shipment'), 'order_id')
            ->where('epace_shipment_id IS NOT NULL')
            ->where('carrier IS NULL OR carrier = ?', 'ups')
            ->where('price IS NULL')
            ->group('order_id');
        $this->connection->query($orderIdsSelect->insertFromSelect($this->getTemporaryTable(), ['entity_id']));

        /** @var Mage_Sales_Model_Resource_Order_Collection $collection */
        $collection = Mage::getResourceModel('sales/order_collection');
        $collection
            ->addFieldToFilter('entity_id', ['in' => $this->connection->select()->from($this->getTemporaryTable(), 'entity_id')])
            ->setPageSize(100);

        $i = 0;
        $size = $collection->getSize();
        $page = 1;
        $lastPage = $collection->getLastPageNumber();

        do {
            $collection->clear()->setCurPage($page)->load();

            /** @var Mage_Sales_Model_Order $order */
            foreach ($collection as $order) {
                $this->writeln(++$i . '/' . $size . ' ' . $order->getId() . ' ' . $order->getEpaceJobId());
                $this->tabs++;
                try {
                    $shipments = $order->getShipmentsCollection()->getItems();
                    $sCount = count($shipments);
                    //$this->writeln($sCount . ' shipments');

                    $changed = false;
                    $shippingPrice = 0;

                    $j = 0;

                    /** @var Mage_Sales_Model_Order_Shipment $shipment */
                    foreach ($shipments as $shipment) {
                        $this->write(++$j . '/' . $sCount . ' ' . $shipment->getId() . ' ' . $shipment->getEpaceShipmentId() . ' ');
                        if ($shipment->getPrice()) {
                            $shippingPrice += $shipment->getPrice();
                            $this->writeln('Price = ' . $shipment->getPrice() . '. Skip.');
                            continue;
                        }

                        if (!$shipment->getCarrier() || !$shipment->getMethod()) {
                            /** @var Blackbox_Epace_Model_Epace_Job_Shipment $epaceShipment */
                            $epaceShipment = Mage::getModel('efi/job_shipment')->load($shipment->getEpaceShipmentId());
                            if (!$epaceShipment->getId()) {
                                $this->writeln('Epace shipment not found. Skip.');
                                continue;
                            }

                            $this->write($epaceShipment->getShipVia()->getShipProvider()->getName() . ' - ' . $epaceShipment->getShipVia()->getDescription() . ' ');

                            $shippingRateResult = $this->helper->getShippingMethod($epaceShipment->getShipVia());
                            if (!$shippingRateResult->getCarrier() || !$shippingRateResult->getMethod()) {
                                $this->writeln('Unable to recognize carrier and method. Skip.');
                                continue;
                            }

                            $shipment->setCarrier($shippingRateResult->getCarrier())->setMethod($shippingRateResult->getMethod());
                            $save = true;
                        } else {
                            $this->write($shipment->getCarrier() . ' - ' . $shipment->getMethod() . ' ');
                        }

                        $shipment->setOrder($order);
                        try {
                            $prices = $this->helper->getShipmentPrice($shipment);
                            if ($prices) {
                                $shippingPrice += $prices['price'];
                                $changed = true;

                                $shipment->setPrice($prices['price'])
                                    ->setCost($prices['cost']);
                                $save = true;

                                $this->write('Price: ' . $prices['price'] . ' ');
                            } else {
                                $this->write('Price not loaded. ');
                            }
                        } catch (\Exception $e) {
                            $this->write('Error while loading price: ' . $e->getMessage() . ' ');
                        }

                        if ($save) {
                            $this->writeln('Saved.');
                            $shipment->save();
                        } else {
                            $this->writeln('');
                        }
                    }

                    if ($changed) {
                        $order->addData([
                            'base_shipping_amount' => $shippingPrice,
                            'shipping_amount' => $shippingPrice
                        ]);
                        //$order->save();
                    }
                } finally {
                    $this->tabs--;
                }
            }
        } while (++$page <= $lastPage);
    }

    protected function createTemporaryTable()
    {
        $table = $this->connection->newTable($this->getTemporaryTable())
            ->addColumn(
                'entity_id',
                Varien_Db_Ddl_Table::TYPE_INTEGER,
                null,
                array(
                    'unsigned' => true
                ),
                'Shipment ID'
            );
        $this->connection->createTemporaryTable($table);
    }

    protected function getTemporaryTable()
    {
        return 'import_shipment_prices_order_ids_temp';
    }

    protected function write($msg)
    {
        if ($this->newLine) {
            echo str_repeat("\t", $this->tabs) . $msg;
        } else {
            echo $msg;
        }
        $this->newLine = false;
    }

    protected function writeln($msg)
    {
        if ($this->newLine) {
            echo str_repeat("\t", $this->tabs) . $msg . PHP_EOL;
        } else {
            echo $msg . PHP_EOL;
        }
        $this->newLine = true;
    }
}

$shell = new Blackbox_ImportShipmentPrices();
$shell->run();