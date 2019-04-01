<?php

require_once 'abstract.php';

class EpaceImportCsrs extends Mage_Shell_Abstract
{
    /**
     * @var Mage_Core_Model_Resource
     */
    protected $resource;

    /**
     * @var Varien_Db_Adapter_Interface
     */
    protected $connection;

    /**
     * @var Blackbox_EpaceImport_Helper_Data
     */
    protected $helper;

    public function __construct()
    {
        parent::__construct();

        $this->resource = Mage::getSingleton('core/resource');
        $this->connection = $this->resource->getConnection('core_write');
        $this->helper = Mage::helper('epacei');
    }

    public function run()
    {
        if ($this->getArg('mongo')) {
            Blackbox_Epace_Model_Epace_AbstractObject::$useMongo = true;
        }

        $this->writeln('Orders');
        $this->importCsrs('efi/job', $this->resource->getTableName('sales/order'), 'entity_id', 'epace_job_id', 'csr_id');

        $this->writeln('Estimates');
        $this->importCsrs('efi/estimate', $this->resource->getTableName('epacei/estimate'), 'entity_id', 'epace_estimate_id', 'csr_id');
    }

    protected function importCsrs($epaceClass, $table, $primaryKey, $epaceKey, $csrKey)
    {
        $select = $this->connection->select()->from($table, [$primaryKey, $epaceKey])
            ->where($epaceKey . ' IS NOT NULL')
            ->where($csrKey . ' IS NULL');

        $rows = $this->connection->fetchAll($select);
        $count = count($rows);
        $this->writeln('Found ' . $count . ' entities.');
        $i = 0;

        foreach ($rows as $row) {
            $this->write(++$i . '/' . $count . ' ' . $row[$primaryKey]);
            $object = Mage::getModel($epaceClass)->load($row[$epaceKey]);
            if (!$object->getId()) {
                $this->writeln(' Object ' . $row[$epaceKey] . ' not found.');
                continue;
            }

            if (!$object->getCSR()) {
                $this->writeln(' Object ' . $row[$epaceKey] . ' does not have CSR.');
                continue;
            }
            $customer = $this->helper->getCustomerFromCSR($object->getCSR());
            $this->writeln(' customer ' . $customer->getId());

            $this->connection->update($table, [
                $csrKey => $customer->getId()
            ], [
                $primaryKey . ' = ?' => $row[$primaryKey]
            ]);
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

$shell = new EpaceImportCsrs();
$shell->run();