<?php

include 'abstract.php';

class BlackBox_Shell_EpaceImport extends Mage_Shell_Abstract
{
    public function run()
    {
        $this->dump();
    }

    protected function dump()
    {
        $json = [];
        $customers = [];
        $salesPersons = [];
        $csrs = [];

        /** @var Blackbox_Epace_Model_Resource_Epace_Estimate_Collection $collection */
        $collection = Mage::getResourceModel('efi/estimate_collection');
        //$collection->addFilter('entryDate', ['gt' => new DateTime($this->getArg('date'))]);
        $collection
            ->setOrder('entryDate', Varien_Data_Collection::SORT_ORDER_DESC)
            ->setOrder('entryTime', Varien_Data_Collection::SORT_ORDER_DESC);
        $collection->setPageSize(1)->setCurPage(1);
        $collection->addFilter('id', 52434);
        //$collection->addFilter('id', 11547);
        //$collection->addFilter('id', 10883);

        foreach ($collection->getItems() as $estimate) {
            $estimateData = $estimate->getData();

            $customerId = $estimate->getData('customer');
            if (!isset($customers[$customerId])) {
                $customers[$customerId] = $estimate->getCustomer() ? $estimate->getCustomer()->getData() : false;
            }

            $salesPersonId = $estimate->getData('salesPerson');
            if (!isset($salesPersons[$salesPersonId])) {
                $salesPersons[$salesPersonId] = $estimate->getSalesPerson() ? $estimate->getSalesPerson()->getData() : false;
            }

            $csrId = $estimate->getData('csr');
            if (!isset($csrs[$csrId])) {
                $csrs[$csrId] = $estimate->getCSR() ? $estimate->getCSR()->getData() : false;
            }

            foreach ($estimate->getProducts() as $product) {
                $productData = $product->getData();
                foreach ($product->getPriceSummaries() as $priceSummary) {
                    $productData['priceSummaries'][] = $priceSummary->getData();
                }
                foreach ($product->getParts() as $part) {
                    $partData = $part->getData();
                    foreach ($part->getSizeAllowances() as $sizeAllowance) {
                        $partData['sizeAllowances'][] = $sizeAllowance->getData();
                    }
                    foreach ($part->getQuantities() as $quantity) {
                        $partData['quanitites'][] = $quantity->getData();
                    }
                    $productData['parts'][] = $partData;
                }
                $estimateData['products'][] = $productData;
            }

            if ($estimate->isConvertedToJob()) {
                $job = $estimate->getJob();
                if ($job) {
                    $jobData = $job->getData();
                    foreach ($job->getProducts() as $product) {
                        $jobData['products'][] = $product->getData();
                    }
                    foreach ($job->getJobContacts() as $jobContact) {
                        $jobContactData = $jobContact->getData();
                        $jobContactData['_contact'] = $jobContact->getContact()->getData();
                        $customerId = $jobContact->getContact()->getData('customer');
                        if (!isset($customers[$customerId])) {
                            $customer = $jobContact->getContact()->getCustomer();
                            $customers[$customerId] = $customer ? $customer->getData() : false;
                        }
                        $jobData['jobContacts'][] = $jobContactData;
                    }
                    foreach ($job->getShipments() as $shipment) {
                        $shipmentData = $shipment->getData();
                        $shipmentData['_shipmentType'] = $shipment->getType()->getData();
                        $shipmentData['_shipVia'] = $shipment->getShipVia()->getData();
                        foreach ($shipment->getCartons() as $carton) {
                            $cartonData = $carton->getData();
                            foreach ($carton->getContents() as $content) {
                                $cartonData['contents'][] = $content->getData();
                            }
                            $shipmentData['cartons'][] = $cartonData;
                        }
                        $jobData['shipments'][] = $shipmentData;
                    }
                    foreach ($job->getNotes() as $note) {
                        $jobData['notes'][] = $note->getData();
                    }
                    foreach ($job->getParts() as $part) {
                        $partData = $part->getData();
                        foreach ($part->getMaterials() as $material) {
                            $partData['materials'][] = $material->getData();
                        }
                        foreach ($part->getPrePressOps() as $prePressOp) {
                            $partData['prePressOps'][] = $prePressOp->getData();
                        }
                        foreach ($part->getPressForms() as $pressForm) {
                            $partData['pressForms'][] = $pressForm->getData();
                        }
                        foreach ($part->getFinishingOps() as $finishingOp) {
                            $partData['finishingOps'][] = $finishingOp->getData();
                        }
                        foreach ($part->getOutsidePurchs() as $outsidePurch) {
                            $partData['outsidePurchs'][] = $outsidePurch->getData();
                        }
                        foreach ($part->getPlans() as $plan) {
                            $partData['plans'][] = $plan->getData();
                        }
                        foreach ($part->getCosts() as $cost) {
                            $partData['costs'][] = $cost->getData();
                        }
                        foreach ($part->getSizeAllowances() as $sizeAllowance) {
                            $partData['jobPartSizeAllowances'][] = $sizeAllowance->getData();
                        }
                        $jobData['parts'][] = $partData;
                    }
                    $estimateData['job'] = $jobData;
                }
            }

            $json['estimates'][] = $estimateData;
        }

        $json['customers'] = $customers;
        $json['salesPersons'] = $salesPersons;
        $json['csrs'] = $csrs;

//        /** @var Blackbox_Epace_Model_Resource_Epace_Job_Collection $collection */
//        $collection = Mage::getResourceModel('efi/job_collection');
//        $collection->setOrder('dateSetup', Varien_Data_Collection::SORT_ORDER_DESC);
//        $collection->setPageSize(1)->setCurPage(1);
//
//        foreach ($collection->getItems() as $job) {
//            $jobData = $job->getData();
//
//            $quote = $job->getQuote();
//            if ($quote) {
//                $jobData['quote'] = $quote->getData();
//            }
//
//            $json['jobs'][] = $jobData;
//        }

//        /** @var Blackbox_Epace_Model_Resource_Epace_Quote_Collection $collection */
//        $collection = Mage::getResourceModel('efi/quote_collection');
//        $collection->setOrder('requestDate', Varien_Data_Collection::SORT_ORDER_DESC);
//        $collection->setPageSize(1)->setCurPage(1);
//
//        foreach ($collection->getItems() as $quote) {
//            $quoteData = $quote->getData();
//
//            $json['quotes'][] = $quoteData;
//        }

        echo json_encode($json, JSON_PRETTY_PRINT);

        /** @var Blackbox_Epace_Helper_Api $api */
//        $api = Mage::helper('epace/api');
//
//        $quantity = $api->readObject('estimateQuantity', [
//            'id' => 163221
//        ]);
//
//        var_dump($quantity);

//        $product = $api->readObject('estimateProduct', [
//            'id' => 53174
//        ]);
//
//        var_dump($product);

//        $date = new DateTime($this->getArg('date'));
//
//        $estimates = $api->findObjects('Estimate', '@entryDate > date( ' . $date->format('Y, m, d') . ' )');
//
//        foreach ($estimates as $estimateId) {
//            $estimate = $api->readEstimate($estimateId);
//            var_dump($estimate);
//        }
    }

    protected function import()
    {
        $from = $this->getArg('from');
        $to = $this->getArg('to');

        /** @var Blackbox_Epace_Model_Resource_Epace_Estimate_Collection $collection */
        $collection = Mage::getResourceModel('efi/estimate_collection');
        if ($from) {
            $collection->addFilter('entryDate', ['gt' => new DateTime($from)]);
        }
        if ($to) {
            $collection->addFilter('entryDate', ['lt' => new DateTime($to)]);
        }

        $ids = $collection->getAllIds();
        $count = count($ids);
        $i = 0;
        $this->writeln('Found ' . $count . ' estimates.');
        foreach ($ids as $estimateId) {
            $this->writeln('Estimate ' . ++$i . '/' . $count);
            /** @var Blackbox_Epace_Model_Epace_Estimate $estimate */
            $estimate = Mage::getModel('efi/estimate')->load($estimateId);
            $this->importEstimate($estimate);
        }
    }

    protected function importEstimate(Blackbox_Epace_Model_Epace_Estimate $estimate)
    {

    }

    protected function importJob(Blackbox_Epace_Model_Epace_Job $job)
    {

    }

    protected function importShipment(Blackbox_Epace_Model_Epace_Job_Shipment $shipment)
    {

    }

    protected function writeln($message)
    {
        echo $message . PHP_EOL;
    }
}

$shell = new BlackBox_Shell_EpaceImport();
$shell->run();