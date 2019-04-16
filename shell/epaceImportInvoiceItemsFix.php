<?php

require_once 'abstract.php';

class Blackbox_Shell_EpaceImportInvoiceItemsFix extends Mage_Shell_Abstract
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
        $invoiceItemTable = $resource->getTableName('sales/invoice_item');

        $select = $connection->select()->from(['i' => $invoiceTable], ['entity_id', 'epace_invoice_id'])
            ->where('entity_id IN (' . $connection->select()->from($invoiceItemTable, 'parent_id')->where('epace_invoice_line_id IS NULL') . ')')
            ->where('epace_invoice_id IS NOT NULL');

        $invoices = $connection->fetchAll($select);

        $count = count($invoices);
        $this->writeln('Found ' . $count . ' invoices');
        $i = 0;

        foreach ($invoices as $invoiceRow) {
            $this->write(++$i . '/' . $count . ' ' . $invoiceRow['entity_id'] . ' ' . $invoiceRow['epace_invoice_id']);
            /** @var Blackbox_Epace_Model_Epace_Invoice $invoice */
            $invoice = Mage::getModel('efi/invoice')->load($invoiceRow['epace_invoice_id']);
            if (!$invoice->getId()) {
                $this->writeln(' Not found.');
                continue;
            }

            $this->writeln('');
            $j = 0;
            foreach ($invoice->getLines() as $line) {
                $this->write("\tLine " . ++$j . ' ' . $line->getId());
                $select = $connection->select()->from($invoiceItemTable, ['entity_id'])
                    ->where('parent_id = ?', $invoiceRow['entity_id'])
                    ->where('name = ?', $line->getDescription());

                $itemId = $connection->fetchOne($select);
                if (!$itemId) {
                    $this->writeln(' Not found.');
                    continue;
                }

                $this->writeln(' ' . $itemId);
                $connection->update($invoiceItemTable, [
                    'epace_invoice_line_id' => $line->getId()
                ], [
                    'entity_id = ?' => $itemId
                ]);
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

$shell = new Blackbox_Shell_EpaceImportInvoiceItemsFix();
$shell->run();