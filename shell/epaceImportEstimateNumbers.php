<?php
require_once 'abstract.php';

class Shell_EpaceImportEstimateNumbers extends Mage_Shell_Abstract
{
    public function run()
    {
        if ($this->getArg('mongo')) {
            Blackbox_Epace_Model_Epace_AbstractObject::$useMongo = true;
        }

        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');

        $table = $resource->getTableName('epacei/estimate');
        $select = $connection->select()->from($table, ['entity_id', 'epace_estimate_id'])
            ->where('epace_estimate_id IS NOT NULL')
            ->where('estimate_number IS NULL');

        $rows = $connection->fetchAll($select);

        $i = 0;
        $count = count($rows);
        $this->writeln('Found ' . $count . ' estimates');

        foreach ($rows as $row) {
            $this->write(++$i . '/' . $count . ' ' . $row['entity_id'] . ' ' . $row['epace_estimate_id']);
            /** @var Blackbox_Epace_Model_Epace_Estimate $estimate */
            $estimate = Mage::getModel('efi/estimate')->load($row['epace_estimate_id']);
            if (is_null($estimate->getId())) {
                $this->writeln(' Not found.');
                continue;
            }

            $this->writeln(' ' . $estimate->getEstimateNumber() . ' ' . $estimate->getEstimateVersionNumber());
            $connection->update($table, [
                'estimate_number' => $estimate->getEstimateNumber(),
                'version' => $estimate->getEstimateVersionNumber()
            ], [
                'entity_id = ?' => $row['entity_id']
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

$shell = new Shell_EpaceImportEstimateNumbers();
$shell->run();