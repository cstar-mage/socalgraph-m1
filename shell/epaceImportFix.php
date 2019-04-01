<?php
require_once 'abstract.php';

class EpaceImportFix extends Mage_Shell_Abstract
{
    public function run()
    {
        if ($this->getArg('mongo')) {
            Blackbox_Epace_Model_Epace_AbstractObject::$useMongo = true;
        }

        if ($this->getArg('btst_job_contacts')) {
            $this->importBillToAndShipToJobContacts();
        }

        if ($this->getArg('job_description')) {
            $this->importJobDescriptions();
        }
    }

    protected function importBillToAndShipToJobContacts()
    {
        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');

        $table = $resource->getTableName('sales/order');
        $select = $connection->select()->from($table, ['entity_id', 'epace_job_id'])
            ->where('epace_job_id IS NOT NULL')
            ->where('epace_bill_to_job_contact IS NULL OR epace_ship_to_job_contact IS NULL');

        $rows = $connection->fetchAll($select);
        $count = count($rows);
        $this->writeln('Found ' . $count . ' orders.');
        $i = 0;

        foreach ($rows as $row) {
            $this->write(++$i . '/' . $count . ': ' . $row['entity_id']);
            /** @var Blackbox_Epace_Model_Epace_Job $job */
            $job = Mage::getModel('efi/job')->load($row['epace_job_id']);
            if (!$job->getId()) {
                $this->writeln(' Not found.');
                continue;
            }

            $this->writeln(' ' . (int)$job->getBillToJobContactId() . ' ' . (int)$job->getShipToJobContactId());

            $connection->update($table, [
                'epace_bill_to_job_contact' => $job->getBillToJobContactId(),
                'epace_ship_to_job_contact' => $job->getShipToJobContactId()
            ], [
                'entity_id = ?' => $row['entity_id']
            ]);
        }
    }

    protected function importJobDescriptions()
    {
        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');

        $table = $resource->getTableName('sales/order');
        $select = $connection->select()->from($table, ['entity_id', 'epace_job_id'])
            ->where('epace_job_id IS NOT NULL')
            ->where('epace_job_description IS NULL');

        $rows = $connection->fetchAll($select);
        $count = count($rows);
        $this->writeln('Found ' . $count . ' orders.');
        $i = 0;

        foreach ($rows as $row) {
            $this->write(++$i . '/' . $count . ': ' . $row['entity_id']);
            /** @var Blackbox_Epace_Model_Epace_Job $job */
            $job = Mage::getModel('efi/job')->load($row['epace_job_id']);
            if (!$job->getId()) {
                $this->writeln(' Not found.');
                continue;
            }

            $this->writeln(' ' . $job->getDescription());

            $connection->update($table, [
                'epace_job_description' => $job->getDescription()
            ], [
                'entity_id = ?' => $row['entity_id']
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

$shell = new EpaceImportFix();
$shell->run();