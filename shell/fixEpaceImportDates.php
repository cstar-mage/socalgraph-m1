<?php

require_once 'abstract.php';

class Shell_FixEpaceImportDates extends Mage_Shell_Abstract
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

        if ($this->getArg('e')) {
            $this->writeln('Estimates.');
            $this->fixEntities('epacei/estimate', 'epace_estimate_id', 'efi/estimate', 'entryDate', 'entryTime', true);
        }
        if ($this->getArg('o')) {
            $this->writeln('Orders');
            $this->fixEntities('sales/order', 'epace_job_id', 'efi/job', 'dateSetup', 'timeSetUp', true);
        }
        if ($this->getArg('i')) {
            $this->writeln('Invoices');
            $this->fixEntities('sales/order_invoice', 'epace_invoice_id', 'efi/invoice', 'dateSetup', 'timeSetup', true);
        }
        if ($this->getArg('r')) {
            $this->writeln('Receivables');
            $this->fixEntities('epacei/receivable', 'epace_receivable_id', 'efi/receivable', 'dateSetup', 'timeSetup', false);
        }
        if ($this->getArg('s')) {
            $this->writeln('Shipments');
            $this->fixEntities('sales/order_shipment', 'epace_shipment_id', 'efi/job_shipment', 'date', 'time', true);
        }
        $this->writeln('Done.');
    }

    protected function fixEntities($magentoModelName, $remoteIdField, $remoteModelName, $dateField, $timeField, $grid)
    {
        /** @var Mage_Core_Model_Abstract $model */
        $model = Mage::getModel($magentoModelName);
        /** @var Mage_Core_Model_Resource_Db_Collection_Abstract $collection */
        $collection = $model->getCollection();
        $collection->addFieldToFilter($remoteIdField, ['notnull' => true]);

        /** @var Blackbox_Epace_Model_Epace_AbstractObject $remoteModel */
        $remoteModel = Mage::getModel($remoteModelName);
        $filter = $this->getArg($remoteModel->getObjectType() . '_filter');
        if ($filter) {
            $filters = json_decode($filter);
            if ($filters) {
                if (!is_array($filters)) {
                    $filters = [$filters];
                }
                foreach ($filters as $filter) {
                    $collection->addFieldToFilter($filter->field, $filter->value);
                }
            }
        }

        /** @var Mage_Core_Model_Resource_Db_Abstract $resource */
        $resource = $model->getResource();
        $table = $resource->getMainTable();
        $connection = $resource->getReadConnection();

        $i = 0;
        $page = 0;
        $collection->setPageSize(100);
        $lastPage = $collection->getLastPageNumber();
        $totalItems = $collection->getSize();

        $this->writeln('Total ' . $totalItems . ' items');

        do {
            $collection->clear()->setCurPage(++$page)->load();

            /** @var Mage_Core_Model_Abstract $item */
            foreach ($collection->getItems() as $item) {
                $this->write(++$i . '/' . $totalItems . ' ' . $item->getId());
                /** @var Blackbox_Epace_Model_Epace_AbstractObject $remoteObject */
                $remoteObject = Mage::getModel($remoteModelName)->load($item->getData($remoteIdField));
                if (!$remoteObject->getId()) {
                    $this->writeln(' ' . $remoteObject->getObjectType() . ' with id ' . $item->getData($remoteIdField) . ' not found.');
                    continue;
                }

                foreach ([$dateField, $timeField] as $field) {
                    if (empty($remoteObject->getData($field))) {
                        $msg = 'Field ' . $field . ' of ' . $remoteObject->getObjectType() . ' ' . $remoteObject->getId() . ' is empty';
                        if ($field == $dateField) {
                            throw new \Exception($msg);
                        } else {
                            $this->writeln($msg);
                        }
                    }
                }

//                $remoteCreatedAt = strtotime($remoteObject->getData($dateField)) + strtotime($remoteObject->getData($timeField));
                $remoteCreatedAt = $this->helper->getTimestamp($remoteObject->getData($dateField), $remoteObject->getData($timeField));
                $localCreatedAt = strtotime($item->getCreatedAt());

                if ($remoteCreatedAt != $localCreatedAt) {
                    $mysqlNewCreatedAt = date('Y-m-d H:i:s', $remoteCreatedAt);
                    $this->writeln(' ' . $item->getCreatedAt() . ' => ' . $mysqlNewCreatedAt);
                    $result = $connection->update($table, [
                        'created_at' => $mysqlNewCreatedAt
                    ], [
                        $model->getIdFieldName() . ' = ?' => $item->getId()
                    ]);
                    if ($grid) {
                        $result = $connection->update($table . '_grid', [
                            'created_at' => $mysqlNewCreatedAt
                        ], [
                            $model->getIdFieldName() . ' = ?' => $item->getId()
                        ]);
                    }
                } else {
                    $this->writeln(' Skip equal.');
                }
            }
        } while ($page < $lastPage);
    }

    protected function write($message)
    {
        echo $message;
    }

    protected function writeln($message)
    {
        echo $message . PHP_EOL;
    }
}

$shell = new Shell_FixEpaceImportDates();
$shell->run();