<?php
require_once 'abstract.php';

class Shell_EpaceImportOrderItemsJobType extends Mage_Shell_Abstract
{
    public function run()
    {
        if ($this->getArg('mongo')) {
            Blackbox_Epace_Model_Epace_AbstractObject::$useMongo = true;
        }

        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');

        $table = $resource->getTableName('sales/order_item');
        $select = $connection->select()->from(['i' => $table], ['item_id', 'o.epace_job_id', 'epace_job_part'])
            ->joinInner(['o' => $resource->getTableName('sales/order')], 'i.order_id = o.entity_id AND o.epace_job_id IS NOT NULL', '')
            ->where('epace_job_part IS NOT NULL')
            ->where('i.job_type IS NULL');

        $rows = $connection->fetchAll($select);

        $i = 0;
        $count = count($rows);
        $this->writeln('Found ' . $count . ' items');

        foreach ($rows as $row) {
            $this->write(++$i . '/' . $count . ' ' . $row['item_id'] . ' ' . $row['epace_job_id'] . ':' . $row['epace_job_part']);
            /** @var Blackbox_Epace_Model_Epace_Job_Part $part */
            $part = Mage::getModel('efi/job_part')->load($row['epace_job_id'] . ':' . $row['epace_job_part']);
            if (is_null($part->getId())) {
                $this->writeln(' Not found.');
                continue;
            }

            $this->writeln(' ' . ' type ' . $part->getJobType());
            $connection->update($table, [
                'job_type' => $part->getJobType(),
            ], [
                'item_id = ?' => $row['item_id']
            ]);
        }
    }

    protected function writeln($msg)
    {
        echo $msg . PHP_EOL;
    }

    protected function write($msg)
    {
        echo $msg;
    }
}

$shell = new Shell_EpaceImportOrderItemsJobType();
$shell->run();
