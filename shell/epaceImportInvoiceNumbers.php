<?php

require_once 'abstract.php';

class Blackbox_Shell_EpaceImportInvoiceNumbers extends Mage_Shell_Abstract
{
    public function run()
    {
        if ($this->getArg('mongo')) {
            Blackbox_Epace_Model_Epace_AbstractObject::$useMongo = true;
        }

        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');

        $invoiceTable = $resource->getTableName('sales/invoice');
        $receivableTable = $resource->getTableName('epacei/receivable');

        $select = $connection->select()->from(['i' => $invoiceTable], ['entity_id', 'epace_invoice_id'])
            ->where('epace_invoice_number IS NULL')
            ->where('epace_invoice_id IS NOT NULL');

        $rows = $connection->fetchAll($select);

        $count = count($rows);
        $this->writeln('Found ' . $count . ' invoices');
        $i = 0;

        foreach ($rows as $row) {
            $this->write(++$i . '/' . $count . ' ' . $row['entity_id'] . ' ' . $row['epace_invoice_id']);
            /** @var Blackbox_Epace_Model_Epace_Invoice $invoice */
            $invoice = Mage::getModel('efi/invoice')->load($row['epace_invoice_id']);
            if (!$invoice->getId()) {
                $this->writeln(' Not found.');
                continue;
            }

            $this->writeln(' ' . $invoice->getInvoiceNum());
            $connection->update($invoiceTable, [
                'epace_invoice_number' => $invoice->getInvoiceNum()
            ], [
                'entity_id = ?' => $row['entity_id']
            ]);
        }

        $this->writeln('');

        $select = $connection->select()->from(['i' => $receivableTable], ['entity_id', 'epace_receivable_id'])
            ->where('invoice_number IS NULL');

        $rows = $connection->fetchAll($select);

        $count = count($rows);
        $this->writeln('Found ' . $count . ' receivables');
        $i = 0;

        foreach ($rows as $row) {
            $this->write(++$i . '/' . $count . ' ' . $row['entity_id'] . ' ' . $row['epace_receivable_id']);
            /** @var Blackbox_Epace_Model_Epace_Receivable $receivable */
            $receivable = Mage::getModel('efi/receivable')->load($row['epace_receivable_id']);
            if (!$receivable->getId()) {
                $this->writeln(' Not found.');
                continue;
            }

            $this->writeln(' ' . $receivable->getInvoiceNumber());
            $connection->update($receivableTable, [
                'invoice_number' => $receivable->getInvoiceNumber()
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

$shell = new Blackbox_Shell_EpaceImportInvoiceNumbers();
$shell->run();