<?php

require_once 'abstract.php';

class Blackbox_Shell_EpaceImportInvoiceFix extends Mage_Shell_Abstract
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
            ->where('receivable_id IS NULL')
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

            if (is_null($invoice->getReceivableId())) {
                $this->writeln(' No receivable.');
                continue;
            }

            $this->write(' ' . $invoice->getReceivableId());

            $select = $connection->select()->from(['r' => $receivableTable], ['entity_id'])
                ->where('epace_receivable_id = ?', $invoice->getReceivableId());
            $receivableId = $connection->fetchOne($select);
            if (!$receivableId) {
                $this->writeln(' Receivable not imported.');
                continue;
            }

            $this->writeln(' ' . $receivableId);
            $connection->update($invoiceTable, [
                'receivable_id' => $receivableId,
                'epace_receivable_id' => $invoice->getReceivableId()
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

$shell = new Blackbox_Shell_EpaceImportInvoiceFix();
$shell->run();