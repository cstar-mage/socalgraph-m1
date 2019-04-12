<?php

require_once 'abstract.php';

class Shell_EpaceImportFromMongo extends Mage_Shell_Abstract
{
    protected $entities = [
        'Estimate' => [
            'keys' => [
                'e',
                'estimates'
            ],
            'dateField' => 'entryDate',
            'magentoClass' => 'epacei/estimate',
            'idField' => 'epace_estimate_id',
            'importMethod' => 'importEstimate',
            'updateMethod' => 'updateEstimate'
        ],
        'Job' => [
            'keys' => [
                'j',
                'jobs'
            ],
            'dateField' => 'dateSetup',
            'magentoClass' => 'sales/order',
            'idField' => 'epace_job_id',
            'importMethod' => 'importJob',
            'updateMethod' => 'updateOrder'
        ],
        'Invoice' => [
            'keys' => [
                'i',
                'invoices'
            ],
            'dateField' => 'invoiceDate',
            'magentoClass' => 'sales/order_invoice',
            'idField' => 'epace_invoice_id',
            'importMethod' => 'importInvoice',
            'updateMethod' => 'updateInvoice'
        ],
        'JobShipment' => [
            'keys' => [
                's',
                'shipments'
            ],
            'dateField' => 'date',
            'magentoClass' => 'sales/order_shipment',
            'idField' => 'epace_shipment_id',
            'importMethod' => 'importShipment',
            'updateMethod' => 'updateShipment'
        ]
    ];

    protected $processed = [];

    /**
     * @var Blackbox_Epace_Helper_Data
     */
    protected $epaceHelper;

    /**
     * @var Blackbox_EpaceImport_Helper_Data
     */
    protected $helper;

    protected $tabs = 0;
    protected $newLine = true;
    protected $logTime = false;

    public function __construct()
    {
        parent::__construct();

        $this->epaceHelper = Mage::helper('epace');
        $this->helper = Mage::helper('epacei');

        $this->helper->setOutput(function ($msg) {
            $this->writeln($msg);
        });
    }

    public function run()
    {
        Blackbox_Epace_Model_Epace_AbstractObject::$useMongo = true;

        if ($this->getArg('time')) {
            $this->logTime = true;
        }

        $update = $this->getArg('update');
        $from = $this->getArg('from');
        $to = $this->getArg('to');

        foreach ($this->entities as $entity => $settings) {
            $found = false;
            foreach ($settings['keys'] as $key) {
                if ($this->getArg($key)) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                continue;
            }

            $this->processEntities($entity, $from, $to, $update);
        }

        $this->writeln('Done.');
    }

    protected function processEntities($name, $from, $to, $update)
    {
        $this->writeln('Processing entities ' . $name);

        $settings = $this->entities[$name];

        $epaceModelType = $this->epaceHelper->getTypeName($name);
        /** @var Blackbox_Epace_Model_Resource_Epace_Collection $epaceCollection */
        $epaceCollection = Mage::getResourceModel('efi/' . $epaceModelType . '_collection');
        if ($from) {
            $epaceCollection->addFilter($settings['dateField'], ['gteq' => new \DateTime($from)]);
        }
        if ($to) {
            $epaceCollection->addFilter($settings['dateField'], ['lteq' => new \DateTime($to)]);
        }

        $ids = $epaceCollection->loadIds();
        $count = count($ids);
        $this->writeln('Found ' . $count . ' entries.');
        $i = 0;

        $this->tabs++;
        try {
            foreach ($ids as $id) {
                $this->write(++$i . '/' . $count . ' ' . $id);

                if (in_array($id, $this->processed[$name])) {
                    $this->writeln(' Already processed.');
                    continue;
                }

                try {
                    $magentoModel = Mage::getModel($settings['magentoClass'])->load($id, $settings['idField']);
                    if ($magentoModel->getId()) {
                        if (!$update) {
                            $this->writeln(' Already imported: ' . $magentoModel->getId() . '.');
                            continue;
                        }
                        $this->writeln(' update');
                        $this->tabs++;
                        try {
                            call_user_func([$this->helper, $settings['updateMethod']], $magentoModel, true);
                        } finally {
                            $this->tabs--;
                        }
                        if ($magentoModel instanceof Mage_Sales_Model_Order_Shipment) {
                            $magentoModel->getOrder()->reset();
                            $magentoModel->dispose();
                            gc_collect_cycles();
                        }
                    } else {
                        $this->writeln(' create');
                        $this->tabs++;
                        try {
                            $epaceModel = Mage::getModel('efi/' . $epaceModelType)->load($id);
                            call_user_func([$this, $settings['importMethod']], $epaceModel);
                        } finally {
                            $this->tabs--;
                        }
                    }

                    $this->processed[$name][] = $id;
                } catch (\Exception $e) {
                    $this->writeln('Error: ' . $e->getMessage());
                }
            }
        } finally {
            $this->tabs--;
        }
    }

    public function importEstimate(Blackbox_Epace_Model_Epace_Estimate $estimate, $checkImported = false)
    {
        /** @var Blackbox_EpaceImport_Model_Estimate $magentoEstimate */
        $magentoEstimate = Mage::getModel('epacei/estimate');
        if ($checkImported) {
            $magentoEstimate->loadByAttribute('epace_estimate_id', $estimate->getId());
            if ($magentoEstimate->getId()) {
                $this->writeln('Estimate ' . $estimate->getId() . ' already imported.');
                return;
            }
        }

        $this->helper->importEstimate($estimate, $magentoEstimate);
        $magentoEstimate->save();

        if ($estimate->isConvertedToJob()) {
            $jobs = $estimate->getJobs();
            if (!empty ($jobs)) {
                $count = count($jobs);
                $this->writeln('Found ' . $count . ' jobs');
                $i = 0;
                $this->tabs++;
                try {
                    foreach ($estimate->getJobs() as $job) {
                        $this->writeln('Job ' . ++$i . '/' . $count . ': ' . $job->getId());
                        if ($job->getEstimateId() != $estimate->getId()) {
                            $this->writeln('Job source does match with estimate.');
                            continue;
                        }
                        $this->importJob($job, $magentoEstimate, true);
                    }
                } finally {
                    $this->tabs--;
                }
            }
        }
    }

    public function importJob(Blackbox_Epace_Model_Epace_Job $job, Blackbox_EpaceImport_Model_Estimate $magentoEstimate = null, $checkImproted = false)
    {
        if (in_array($job->getId(), $this->processed['Job'])) {
            $this->writeln('Already processed.');
            return;
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order');

        if ($checkImproted) {
            $order->loadByAttribute('epace_job_id', $job->getId());
        }

        if ($order->getId()) {
            $this->writeln('Job ' . $job->getId() . ' already imported');
            return;
        }

        $this->helper->importJob($job, $order, $magentoEstimate);
        $order->save();

        $this->tabs++;
        try {
            $i = 0;
            $count = count($job->getInvoices());
            foreach ($job->getInvoices() as $invoice) {
                $i++;
                $this->writeln("Invoice $i/$count: {$invoice->getId()}");
                try {
                    $this->importInvoice($invoice, $order, true);
                } catch (\Exception $e) {
                    $this->writeln('Error: ' . $e->getMessage());
                }
            }

            $i = 0;
            $count = count($job->getShipments());
            foreach ($job->getShipments() as $shipment) {
                $i++;
                $this->writeln("Shipment $i/$count: {$shipment->getId()}");
                try {
                    $this->importShipment($shipment, $order, true);
                } catch (\Exception $e) {
                    $this->writeln('Error: ' . $e->getMessage());
                }
            }
        } finally {
            $this->tabs--;
        }
    }

    public function importShipment(Blackbox_Epace_Model_Epace_Job_Shipment $jobShipment, Mage_Sales_Model_Order $order = null, $checkImported = false)
    {
        if (in_array($jobShipment->getId(), $this->processed['JobShipment'])) {
            $this->writeln('Already processed.');
            return;
        }

        /** @var Mage_Sales_Model_Order_Shipment $orderShipment */
        $orderShipment = Mage::getModel('sales/order_shipment');

        if ($checkImported) {
            $orderShipment->load($jobShipment->getId(), 'epace_shipment_id');
            if ($orderShipment->getId()) {
                $this->writeln("Shipment {$jobShipment->getId()} already imported.");
                return;
            }
        }

        $this->helper->importShipment($jobShipment, $order, $orderShipment);
        $orderShipment->save();
    }

    public function importInvoice(Blackbox_Epace_Model_Epace_Invoice $invoice, Mage_Sales_Model_Order $order = null, $checkImported = false)
    {
        if (in_array($invoice->getId(), $this->processed['Invoice'])) {
            $this->writeln('Already processed.');
            return;
        }

        /** @var Mage_Sales_Model_Order_Invoice $magentoInvoice */
        $magentoInvoice = Mage::getModel('sales/order_invoice');
        if ($checkImported) {
            $magentoInvoice->load($invoice->getId(), 'epace_invoice_id');
            if ($magentoInvoice->getId()) {
                $this->writeln("Invoice {$invoice->getId()} already imported.");
                return;
            }
        }

        $this->helper->importInvoice($invoice, $order, $magentoInvoice);
        $magentoInvoice->save();
//        if ($invoice->getReceivable()) {
//            $this->tabs++;
//            try {
//                $this->helper->importReceivable($invoice->getReceivable(), $magentoInvoice)->save();
//            } finally {
//                $this->tabs--;
//            }
//        }
    }

    protected function write($msg)
    {
        if ($this->newLine) {
            echo ($this->logTime ? date('c') . ' ' : '') . str_repeat("\t", $this->tabs) . $msg;
        } else {
            echo $msg;
        }
        $this->newLine = false;
    }

    protected function writeln($msg)
    {
        if ($this->newLine) {
            echo ($this->logTime ? date('c') . ' ' : '') . str_repeat("\t", $this->tabs) . $msg . PHP_EOL;
        } else {
            echo $msg . PHP_EOL;
        }
        $this->newLine = true;
    }
}

$shell = new Shell_EpaceImportFromMongo();
$shell->run();